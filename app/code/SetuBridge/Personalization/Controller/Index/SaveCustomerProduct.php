<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class SaveCustomerProduct extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \SetuBridge\Personalization\Model\SaveCustomerProduct $saveCustomerProduct,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \SetuBridge\Personalization\Helper\Data $helperData
    ) 
    {
        parent::__construct($context);
        $this->saveCustomerProduct = $saveCustomerProduct;
        $this->personalizeProduct = $personalizeProduct;
        $this->_customerSession = $customerSession;
        $this->_helperData = $helperData;
    }

    public function execute()
    {    
        if($this->_customerSession->create()->isLoggedIn()){
            try {
                $template_data = $this->getRequest()->getPostValue('template_data');
                $productID = $this->getRequest()->getPostValue('productID');
                $customerID = $this->_customerSession->create()->getCustomerData()->getId();
                $title = $this->getRequest()->getPostValue('title');


                /**start*/
                $personalization = $this->saveCustomerProduct
                ->getCollection()
                ->addFieldToFilter('title',$title)
                ->addFieldToFilter('customer_id',$customerID)
                ->getFirstItem();
                if($personalization->getData()){
                    $customerSaveID=$personalization->getData()['customer_save_id'];
                    $this->saveCustomerProduct->setCustomerSaveId($customerSaveID);
                }
                
                /**end*/
                $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);

                if($title){
                    $this->saveCustomerProduct->setTitle($title);
                }

                if($customerID){
                    $this->saveCustomerProduct->setCustomerId($customerID);
                }
                if($productID){
                    $this->saveCustomerProduct->setProductId($productID);
                }
                if($template_data){
                    $this->saveCustomerProduct->setPersonalizationJsonData($template_data);
                }         

                $saveData = $this->saveCustomerProduct->save();
                                
                if($saveData){
                    $this->personalizeProduct->saveCustomerCustomDesignImage($saveData->getCustomerSaveId(),$this->getRequest());
                    $jsonFactory->setData(['error'=>false]);
                    return $jsonFactory;
                }
                else{
                    $jsonFactory->setData(['error' => true]);
                    return $jsonFactory;
                }
            } catch (\Exception $e) {
                $response['message']= $e->getMessage();
            }  
        }
        else{
            $jsonFactory->setData(['error' => true]);
            return $jsonFactory;
        }
    }
} 

