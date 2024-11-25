<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Observer;
use Magento\Framework\Event\ObserverInterface;

class Licence implements ObserverInterface
{
    private $licence;
    protected $messageManager;
    public function __construct(
        \SetuBridge\Personalization\Model\Licence $licence,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->licence = $licence;
        $this->messageManager = $messageManager;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->licence->isNeedToCheck()){
            $res=$this->licence->validateLicence();
            if($res && $res['status'] == false){
                $eMsg=isset($res['msg']) ? $res['msg'] : '';
                if(!empty($eMsg)){
                    if($observer->getEvent()->getName() !='backend_auth_user_login_success'){
                        $this->messageManager->addErrorMessage(__($eMsg));
                    }
                }
            }
        }
    }
}
