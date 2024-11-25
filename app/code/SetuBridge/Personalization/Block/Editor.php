<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Block;
use Magento\Directory\Model\CurrencyFactory;
use SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplateFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;

class Editor extends \Magento\Framework\View\Element\Template
{
    public $_registry;
    const XML_MODULE_CONFIG_PATH='personalization/general/%s'; 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \SetuBridge\Personalization\Helper\Data $helperData,
        CurrencyFactory $currencycode,
        ProductRepositoryInterface $productRepository,
        \SetuBridge\PersonalizationTemplate\Model\ProductEditpageDataFactory $productEditpageDataFactory,
        PersonalizationTemplateFactory $personalizationTemplateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationShapeFactory $shapeFactory,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationFontFactory $fontFactory,
        \SetuBridge\Clipart\Model\ClipartCategoriesFactory $clipartcategoriesFactory,
        \SetuBridge\Clipart\Model\ClipartFactory $clipartFactory,
        \SetuBridge\Color\Model\DataFactory $colorFactory,
        \SetuBridge\TemplateDesigns\Model\TemplateDesignsFactory $templatedesignsFactory,
        \SetuBridge\Personalization\Model\SaveCustomerProductFactory $saveCustomerCollectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Checkout\Model\Cart $_cart,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \SetuBridge\Personalization\Model\PersonalizeProductData $personalizeProductData,
        array $data = []
    ) {
        $this->_registry=$registry;
        parent::__construct($context,$data);
        $this->_storeManager = $context->getStoreManager();
        $this->currencycode= $currencycode;
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
        $this->personalizationTemplateFactory = $personalizationTemplateFactory;
        $this->productEditpageFactory = $productEditpageDataFactory;
        $this->_storeManager = $storeManager;
        $this->localecurrency = $localeCurrency;
        $this->shapeFactory = $shapeFactory;
        $this->fontFactory = $fontFactory;
        $this->clipartcategoriesFactory = $clipartcategoriesFactory;
        $this->clipartFactory = $clipartFactory;
        $this->colorFactory = $colorFactory;
        $this->templatedesignsTemplateDesignsFactory = $templatedesignsFactory;
        $this->saveCustomerCollectionFactory = $saveCustomerCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_logo = $logo;
        $this->_cart = $_cart;
        $this->personalizeProduct =$personalizeProduct;
        $this->serializer =$serializer;
        $this->_personalizeProductData = $personalizeProductData;
    }

    public function registerProduct($productId){
        $product = $this->productRepository->getById($productId);
        $this->_registry->register('current_product',$product); 
        $this->_registry->register('product',$product);
    }

    public function getProduct(){

        $productId = $this->getRequest()->getParam("product");
        if (!$this->_registry->registry('product') && $productId) {
            $product = $this->productRepository->getById($this->getProductId());
            $this->_registry->register('current_product',$product); 
            $this->_registry->register('product',$product);
        }
        return $this->_registry->registry('product');
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
    
    public function generalConfiguration(){
        return $this->helperData->generalConfiguration();
    }

    public function getProductData($productId,$designId = null){

        $this->registerProduct($productId);
        $product = $this->getProduct();

        if ($product) {
            $response=['error'=>false,'data'=>$this->_personalizeProductData->prepareProductInfo($product,$designId)];
        }
        else{
            $response = ['error'=>true];
        }


        return $response; 
    }

    public function getMediaCollection(){

        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        if( $this->_customerSession->create()->isLoggedIn()){
            $customerId = $this->_customerSession->create()->getCustomerData()->getId();
            $myDesignsCollection = $this->saveCustomerCollectionFactory->create()->getCollection()->addFieldToFilter('customer_id',$customerId)->setOrder('customer_save_id', 'desc');

            $newDesignsCollection = [];
            foreach ($myDesignsCollection as $key => $mydesign){
                $mydesign->setImagePath($mediaUrl.'Personalization/CustomerSaveDesign/'.$mydesign->getCustomerSaveId().'/0.png');
                $newDesignsCollection[] = $mydesign->getData();
            }
        }
        else{
            $newDesignsCollection = null;
        }

        if($this->helperData->getPatternActive()){
            $shapeCollection = $this->shapeFactory->create()->getCollection()->addFieldToFilter('status','1');
        }
        else{
            $shapeCollection = [];
            $shapeCollection = $this->convertArrayToObject($shapeCollection);
        }

        if($this->helperData->getTemplateDesignActive()){
            $templatedesignCollection = $this->templatedesignsTemplateDesignsFactory->create()->getCollection()->addFieldToFilter('status','1');
        }
        else{
            $templatedesignCollection = [];
            $templatedesignCollection = $this->convertArrayToObject($templatedesignCollection);
        }

        if($this->helperData->getFontActive()){
            $fontCollection = $this->fontFactory->create()->getCollection()->addFieldToFilter('status','1');
            $fontCollection->setOrder('font_name','ASC');
        }
        else{
            $fontCollection = [];
            $fontCollection = $this->convertArrayToObject($fontCollection);
        }

        $newClipartData = [];
        if($this->helperData->getClipartActive()){
            $clipartCategories = $this->clipartcategoriesFactory->create()->getCollection()->addFieldToFilter('status','1')->setOrder('position','ASC');
            $incr = 1;
            foreach ($clipartCategories as $key => $clipart){
                $newClipartData[$incr] = $clipart->getData();
                $clipartCollection = $this->clipartFactory->create()->getCollection()
                ->addFieldToFilter('category',$clipart->getData('clipartcategories_id'))->addFieldToFilter('status',1)->setOrder('position','DESC');
                $newClipartData[$incr]['clipartcollection'] = $clipartCollection->getData();
                $incr++;
            }


            $allclipartCollection = $this->clipartFactory->create()->getCollection()->addFieldToFilter('status',1)->setOrder('position','DESC')->load();
        }
        else{
            $allclipartCollection = [];
            $allclipartCollection = $this->convertArrayToObject($allclipartCollection);

            $clipartCategories = [];
            $clipartCategories = $this->convertArrayToObject($clipartCategories);
        }

        $color = $this->colorFactory->create()->getCollection();

        return ['patterns' => $shapeCollection->getData(),'templatedesigns' => $templatedesignCollection->getData(),'fonts' => $fontCollection->getData(),'clipartCategoriesData' => $clipartCategories->getData(),'allclipartCollection' => $allclipartCollection->getData(),'colors' => $color->getData(),'clipartCategories' => $newClipartData,'mydesigns'=>$newDesignsCollection,'storage_path' => $mediaUrl,'price_currency_symbol' =>$this->getStoreCurrency()];   
    }

    public function getStoreCurrency(){
        $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        return $this->localecurrency->getCurrency($currencycode)->getSymbol();
    }


    public function convertArrayToObject($array = null){

        if($array){
            $object = new \Magento\Framework\DataObject($array);
            return $object;
        }
        else{
            $object = new \Magento\Framework\DataObject();
            return $object;
        }
    }

    public function isCartEmpty(){
        $quote = $this->_cart->getQuote();
        $totalItems = count($quote->getAllVisibleItems());
        if($totalItems == 0){
            return true;
        }
        return false;
    }

}

