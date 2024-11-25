<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Plugin;

class DefaultRenderer 
{

    protected $personalizationHelper;
    
    public function __construct(
        \SetuBridge\Personalization\Helper\Data $personalizationHelper,
        \Magento\Framework\Registry $registry
    )
    {
        $this->personalizationHelper = $personalizationHelper;
        $this->registry = $registry;
    }

    public function afterGetProductAdditionalInformationBlock($subject,$result){
        if($result){
            if($this->registry->registry('quote_item_id')){
               $this->registry->unregister('quote_item_id'); 
            }
            $this->registry->register('quote_item_id',$subject->getItem()->getQuoteItemId());
            $result->setQuoteItemId($subject->getItem()->getQuoteItemId());
        } 
        return $result; 
    }
}

