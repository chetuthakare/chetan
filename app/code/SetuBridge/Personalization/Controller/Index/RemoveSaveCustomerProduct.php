<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class RemoveSaveCustomerProduct extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \SetuBridge\Personalization\Model\SaveCustomerProductFactory $collectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \SetuBridge\Personalization\Model\ImageProcessor $imageProcessor
    ) 
    {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        $this->imageProcessor =$imageProcessor;
    }

    public function execute()
    {    
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if($this->_customerSession->create()->isLoggedIn()){
            $entityID = $this->getRequest()->getParam('entityID');
            $customerId = $this->_customerSession->create()->getCustomerData()->getId();
            if($entityID){
                $collection = $this->collectionFactory->create()->getCollection()
                ->addFieldToFilter('customer_save_id',$entityID)
                ->addFieldToFilter('customer_id',$customerId)
                ->getFirstItem();
                if(!empty($collection->getData())) {
                    $this->imageProcessor->removeDesignFileDirectory($entityID);
                    $collection->delete();
                    $this->messageManager->addSuccess(__('Design  have been deleted successfully'));
                }
                else{
                    $this->messageManager->addError(__("You can't Design have been delete Please Select valid design"));
                }
            }


            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
        else{
            $this->messageManager->addError(__('Before Your Design Delete Please Login'));
            $this->_redirect('customer/account/login/');
        }
    }
} 

