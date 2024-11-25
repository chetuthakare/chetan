<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
class PersonalizationTemplate extends Generic implements TabInterface
{
    protected $_template = 'ProductEditPage/customization.phtml';

    protected $_systemStore;

    protected $_scopeConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationShapeFactory $shapeFactory,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationFontFactory $fontFactory,
        \SetuBridge\Clipart\Model\ClipartCategoriesFactory $clipartcategoriesFactory,
        \SetuBridge\Clipart\Model\ClipartFactory $clipartFactory,
        \SetuBridge\Color\Model\DataFactory $colorFactory,
        \SetuBridge\Personalization\Helper\Data $helperData,
        \SetuBridge\TemplateDesigns\Model\TemplateDesignsFactory $templatedesignsFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \SetuBridge\PersonalizationTemplate\Model\ProductEditpageDataFactory $productEditpageDataFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_scopeConfig = $scopeConfig;
        $this->_productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->shapeFactory = $shapeFactory;
        $this->fontFactory = $fontFactory;
        $this->clipartcategoriesFactory = $clipartcategoriesFactory;
        $this->clipartFactory = $clipartFactory;
        $this->colorFactory = $colorFactory;
        $this->helperData = $helperData;
        $this->templatedesignsTemplateDesignsFactory = $templatedesignsFactory;
        $this->localecurrency = $localeCurrency;
        $this->_logo = $logo;
        $this->productEditpageFactory = $productEditpageDataFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('Actions');
    }

    public function getTabTitle()
    {
        return __('Actions');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getReqest()
    {
        return $this->getRequest()->getParams();
    }

    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getUniqeId()
    {
        return Date("Ymdhis");
    }

    public function getStoreCurrency(){

        $currencycode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        return $this->localecurrency->getCurrency($currencycode)->getSymbol();
    }

    public function getMediaCollection()
    {
        $shapeCollection = $this->shapeFactory->create()->getCollection()->addFieldToFilter('status','1');
        $templatedesignCollection = $this->templatedesignsTemplateDesignsFactory->create()->getCollection()->addFieldToFilter('status','1');
        $fontCollection = $this->fontFactory->create()->getCollection()->addFieldToFilter('status','1');
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $clipartCategories = $this->clipartcategoriesFactory->create()->getCollection()->addFieldToFilter('status','1');
        $color = $this->colorFactory->create()->getCollection();

        $newClipartData = [];
        foreach ($clipartCategories as $key => $clipart){
            $newClipartData[$key] = $clipart->getData();
            $clipartCollection = $this->clipartFactory->create()->getCollection()
            ->addFieldToFilter('category',$clipart->getData('clipartcategories_id'))->addFieldToFilter('status',1);
            $newClipartData[$key]['clipartcollection'] = $clipartCollection->getData();
        }

        return ['patterns' => $shapeCollection->getData(),'templatedesigns' => $templatedesignCollection->getData(),'fonts' => $fontCollection->getData(),'clipartCategories' => $clipartCategories->getData(),'clipartCategories' => $newClipartData,'colors' => $color->getData(),'storage_path' => $mediaUrl,'price_currency_symbol' =>$this->getStoreCurrency()];
    } 

    public function getGeneralConfiguration(){
        return $this->helperData->generalConfiguration();
    }

    public function isAlreadySavedDesign($productId){

        $personalization  = $this->productEditpageFactory->create()->getCollection()->addFieldToFilter('product_id',$productId)->getFirstItem();

        if($personalization->getPersonalizationJsonData()){
            $jsondata = json_decode($personalization->getPersonalizationJsonData());  
            if($jsondata){
                return true;
            }
        }

        return false;
    }

}