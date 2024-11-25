<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Block\Adminhtml\Grid;

use Magento\Backend\Block\Template;

class TemplateCustomization extends Template
{
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationShapeFactory $shapeFactory,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationFontFactory $fontFactory,
        \SetuBridge\Clipart\Model\ClipartCategoriesFactory $clipartcategoriesFactory,
        \SetuBridge\Clipart\Model\ClipartFactory $clipartFactory,
        \SetuBridge\Color\Model\DataFactory $colorFactory,
        \SetuBridge\Personalization\Helper\Data $helperData,
        \SetuBridge\TemplateDesigns\Model\TemplateDesignsFactory $templatedesignsFactory,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplateFactory $personalizationFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        array $data = []
    ) 
    {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->shapeFactory = $shapeFactory;
        $this->fontFactory = $fontFactory;
        $this->clipartcategoriesFactory = $clipartcategoriesFactory;
        $this->clipartFactory = $clipartFactory;
        $this->colorFactory = $colorFactory;
        $this->helperData = $helperData;
        $this->templatedesignsTemplateDesignsFactory = $templatedesignsFactory;
        $this->personalizationFactory = $personalizationFactory;
        $this->localecurrency = $localeCurrency;
        $this->_logo = $logo;
    }

    public function getStoreCurrency(){

        $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        return $this->localecurrency->getCurrency($currencycode)->getSymbol();
    }

    public function getMediaCollection()
    {
        $shapeCollection = $this->shapeFactory->create()->getCollection()->addFieldToFilter('status','1');
        $templatedesignCollection = $this->templatedesignsTemplateDesignsFactory->create()->getCollection()->addFieldToFilter('status','1');
        $fontCollection = $this->fontFactory->create()->getCollection()->addFieldToFilter('status','1');
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
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

    public function getTemplateData($templateId){

        if($templateId != 0){
            $personalization  = $this->personalizationFactory->create()->load($templateId);
            $jsondata = json_decode($personalization->getPersonalizationJsonData());
        }
        else{
            return ['error' => false];
        }
        if($jsondata){
            return ['template_data' => $jsondata,'error'=>false];
        }
        else{
            return ['error' => true];

        }
    }
}