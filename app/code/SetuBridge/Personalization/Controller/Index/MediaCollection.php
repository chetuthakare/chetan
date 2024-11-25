<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class MediaCollection extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationShapeFactory $shapeFactory,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationFontFactory $fontFactory,
        \SetuBridge\Clipart\Model\ClipartCategoriesFactory $clipartcategoriesFactory,
        \SetuBridge\Clipart\Model\ClipartFactory $clipartFactory,
        \SetuBridge\Color\Model\DataFactory $colorFactory

    ) 
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->shapeFactory = $shapeFactory;
        $this->fontFactory = $fontFactory;
        $this->clipartcategoriesFactory = $clipartcategoriesFactory;
        $this->clipartFactory = $clipartFactory;
        $this->colorFactory = $colorFactory;
    }

    public function execute()
    {    
        $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);
        $shapeCollection = $this->shapeFactory->create()->getCollection()->addFieldToFilter('status','1');
        $fontCollection = $this->fontFactory->create()->getCollection()->addFieldToFilter('status','1');
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $clipartCategories = $this->clipartcategoriesFactory->create()->getCollection()->addFieldToFilter('status','1');
        $color = $this->colorFactory->create()->getCollection();
        
        $newClipartData = [];
        foreach ($clipartCategories as $key => $clipart){
            $newClipartData[$key] = $clipart->getData();
            $clipartCollection = $this->clipartFactory->create()->getCollection()
            ->addFieldToFilter('category',$clipart->getData('clipartcategories_id'))->addFieldToFilter('status',1);
            $clipartCollection->setOrder('position','ASC');
            $newClipartData[$key]['clipartcollection'] = $clipartCollection->getData();
        }
        
        $jsonFactory->setData(['patterns' => $shapeCollection->getData(),'fonts' => $fontCollection->getData(),'clipartCategories' => $clipartCategories->getData(),'clipartCategories' => $newClipartData,'colors' => $color->getData(),'storage_path' => $mediaUrl]);
        return $jsonFactory;
    }

} 

