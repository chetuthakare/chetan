<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Customer;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute() { 
        if($this->customerSession->isLoggedIn()) {
            $this->_view->loadLayout();          
            $this->_view->renderLayout();               
        }
        else{
            $this->_redirect('customer/account/login/');
        }
    } 
} 
