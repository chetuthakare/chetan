<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/ 
namespace SetuBridge\Personalization\Block\Adminhtml\Customer\Grid\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_storeManager;


    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \SetuBridge\Personalization\Helper\Data $helperData,     
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;        
        $this->helperData = $helperData;        
    }


    public function render(\Magento\Framework\DataObject $row)
    {
        $img = '';
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        if($this->_getValue($row)!=''){
            $previewImages = $this->helperData->getCustomDesignImages($this->_getValue($row));
            
            if(!empty($previewImages)) :
                foreach($previewImages as $image) :
                    $img .= '<img class="personalization-preview-img" src="'.$image.'" width="100" height="100"/>';
                    
                    endforeach;
                endif; 
        }
        return $img;
    }
}