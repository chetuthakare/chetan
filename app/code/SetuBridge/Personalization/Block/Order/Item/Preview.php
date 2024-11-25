<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Block\Order\Item;
class Preview extends \Magento\Framework\View\Element\Template
{
    public $_registry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_registry=$registry;
        parent::__construct($context,$data);
    }
    public function getQuoteItemId(){
        return $this->_registry->registry('quote_item_id'); 
    }
}

