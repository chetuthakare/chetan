<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Observer;

use Magento\Framework\Event\ObserverInterface;

class Productunsetdata implements ObserverInterface
{
    protected $messageManager;
    protected $_request;
    protected $model;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \SetuBridge\PersonalizationTemplate\Model\ProductEditpageData $model,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->_request = $context->getRequest();
        $this->model = $model;
        $this->messageManager = $messageManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product = $observer->getProduct();
    }
}
