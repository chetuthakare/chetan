<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Block;
use Magento\Directory\Model\CurrencyFactory;

class Customize extends \Magento\Framework\View\Element\Template
{
    public $_registry;
    const XML_MODULE_CONFIG_PATH='personalization/general/%s'; 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        CurrencyFactory $currencycode,
        \SetuBridge\PersonalizationTemplate\Model\ProductEditpageDataFactory $productEditpageDataFactory,
        \SetuBridge\Personalization\Helper\Data $helperData,  
        array $data = []
    ) {
        $this->_registry=$registry;
        parent::__construct($context,$data);
        $this->_storeManager = $context->getStoreManager();
        $this->currencycode= $currencycode;
        $this->helperData= $helperData;    
        $this->productEditpageFactory = $productEditpageDataFactory;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
    }
    public function getProduct(){
        return $this->_registry->registry('current_product'); 
    }
    public function getStoreId(){
        return $this->_storeManager->getStore()->getId();
    }
    public function getStore(){
        return $this->_storeManager->getStore();
    }
    public function getSymbol(){
        $currentCurrency= $this->_storeManager->getStore()->getCurrentCurrencyCode();
        $currency = $this->currencycode->create()->load($currentCurrency);
        return $currency->getCurrencySymbol();
    }
    public function getConfigValue($path){
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getGeneralConfig($path){
        return $this->getConfigValue(sprintf(self::XML_MODULE_CONFIG_PATH,$path),\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getStatus(){
        return  $this->helperData->getStatus();
    }

    public function getConfig()
    {
        return [
            'addtoCartUrl' => $this->getUrl('personalization/cart/add'),
            'baseUrl' => $this->getBaseUrl()
        ];
    }

    public function checkedCustomization()
    {
        $product = $this->getProduct();
        return $this->helperData->checkedCustomization($product);
    }
}

