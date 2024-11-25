<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*/
namespace SetuBridge\Personalization\Model;
use Magento\Framework\Module\ModuleListInterface;
class Licence extends \Magento\Framework\DataObject
{
    const EXTENSION_SECTION_NAME="personalization";
    const EXTENSION_KEY_CONFIG="personalization/licence/activation_key";
    const EXTENSION_LM_STATUS="personalization/lm/status";
    const XMP_PATH_MODULE_ACTIVE="personalization/general/active";
    const URI='http://store.setubridge.com/licence_api.php/api/checklicence';
    const CONFIG_SETTING_URL_PATH="adminhtml/system_config/edit/section/";
    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;
    protected $_scopeConfig;
    /**
    * @var \Magento\Framework\App\Config\ValueInterface
    */
    protected $_backendModel;
    /**
    * @var \Magento\Framework\DB\Transaction
    */
    protected $_transaction;
    /**
    * @var \Magento\Framework\App\Config\ValueFactory
    */
    protected $_storeId;
    /**
    * @var string $_storeCode
    */
    protected $_storeCode;
    /**
    * @var \Magento\Framework\App\Request\Http $request
    */
    protected $_request;
    protected $urlBuilder;
    protected $extName;

    /**
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
    * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    * @param \Magento\Framework\App\Config\ValueInterface $backendModel,
    * @param \Magento\Framework\DB\Transaction $transaction,
    * @param array $data
    */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\ValueInterface $backendModel,
        \Magento\Framework\DB\Transaction $transaction,
        \Laminas\Http\ClientFactory $httpClientFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        ModuleListInterface $moduleList,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\UrlInterface $url,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_backendModel = $backendModel;
        $this->_transaction = $transaction;
        $this->_storeId=(int)$this->_storeManager->getStore()->getId();
        $this->_storeCode=$this->_storeManager->getStore()->getCode();
        $this->httpClientFactory = $httpClientFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_moduleList = $moduleList;
        $this->_request = $request;
        $this->urlBuilder = $url;
    }
    /**
    * Function for getting Config value of current store
    * @param string $path,
    */
    public function getCurrentStoreConfigValue($path){
        return $this->_scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getRequest(){
        return $this->_request;
    }
    /**
    * Function for setting Config value of current store
    * @param string $path,
    * @param string $value,
    */
    public function setCurrentStoreConfigValue($path,$value,$storeId=null){
        $data = [
            'path' => $path,
            'scope' =>  $this->_storeId ? 'stores':'default',
            'scope_id' => $this->_storeId,
            'scope_code' => $this->_storeCode,
            'value' => $value,
        ];
        $this->_backendModel->addData($data);
        $this->_transaction->addObject($this->_backendModel);
        $this->_transaction->save();
    }
    private function getApiParams(){
        return [
            "domain"=>$this->getParsUrl($this->getRequest()->getServer('SERVER_NAME')),
            "key"=>$this->getCurrentStoreConfigValue(self::EXTENSION_KEY_CONFIG),
            "cur_uri"=>$this->getRequest()->getServer('HTTP_REFERER'),
            "version"=>(string)$this->getExtensionVersion(),
            "requested_ext"=>$this->extName
        ];
    }
    private function getParsUrl($url){
        $host = @parse_url($url, PHP_URL_HOST);
        if (!$host)
            $host = $url;
        if (substr($host, 0, 4) == "www.")
            $host = substr($host, 4);
        return $host;
    }
    private function getExtensionVersion()
    {
        return $this->_moduleList->getOne($this->getExtensionName())['setup_version'];
    }
    public function getExtensionName($separator='_'){
        $classPath = explode('\\', __CLASS__);
        $this->extName=strtolower($classPath[1]).'m2';
        return sprintf("%s%s%s",$classPath[0],$separator,$classPath[1]);
    }
    private function lockStatus($status=0){
        $values=[];
        $values['lm_status']=$status;
        $values['last_update']=date('Y-m-d');
        $this->setCurrentStoreConfigValue(self::EXTENSION_LM_STATUS,json_encode($values));
        if(!$status){
            $this->disableModule();
        }
    }
    private function disableModule(){
        $this->setCurrentStoreConfigValue(self::XMP_PATH_MODULE_ACTIVE,0,0);
    }
    private function getConfig(){
        $values=$this->getCurrentStoreConfigValue(self::EXTENSION_LM_STATUS);
        if(!empty($values)){
            return json_decode($values,true);
        }
        return [];
    }
    public function isNeedToCheck(){
        $result=$this->getConfig();
        if(!empty($result)){
            if(isset($result['last_update']) && !empty($result['last_update'])) {
                if($result['lm_status']==0){
                    return true;
                }
                else if($result['last_update'] > date('Y-m-d') || $result['last_update'] < date('Y-m-d')){
                    return true;
                }
                else{
                    return false;
                }
            }
        }
        else{
            return true;
        }

    }
    public function isValidLicence(){
        $result=$this->getConfig();
        if(count($result) > 0){
            if(isset($result['lm_status'])) return $result['lm_status'];
        }
        return false;
    }
    public function validateLicence(){
        try{
            $httpClient = $this->httpClientFactory->create();
            $response = $httpClient->setUri(
                self::URI
            )
            ->setParameterPost($this->getApiParams())
            ->setOptions(
                [
                    'timeout' => 120
                ]
            )->setMethod(
            	'POST'
            )->send()->getBody();
            $result=$this->jsonHelper->jsonDecode($response);
            $status=isset($result['status'])?$result['status']:0;
            $this->lockStatus($status);
            return $result;
        }catch(\Exception $e){
            return false;
        }
    }
    public function getModuleSettingUrl(){
        return $this->urlBuilder->getUrl(self::CONFIG_SETTING_URL_PATH,['section'=>self::EXTENSION_SECTION_NAME]);
    }

}
