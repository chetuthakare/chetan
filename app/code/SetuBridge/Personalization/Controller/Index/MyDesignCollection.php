<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class MyDesignCollection extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SetuBridge\Personalization\Model\SaveCustomerProductFactory $saveCustomerCollectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession

    ) 
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->saveCustomerCollectionFactory = $saveCustomerCollectionFactory;
        $this->_customerSession = $customerSession;
    }

    public function execute()
    {    
        $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);

        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        if( $this->_customerSession->create()->isLoggedIn()){
            $customerId = $this->_customerSession->create()->getCustomerData()->getId();
            $myDesignsCollection = $this->saveCustomerCollectionFactory->create()->getCollection()->addFieldToFilter('customer_id',$customerId)->setOrder('customer_save_id', 'desc');

            $newDesignsCollection = [];
            foreach ($myDesignsCollection as $key => $mydesign){
                $mydesign->setImagePath($mediaUrl.'Personalization/CustomerSaveDesign/'.$mydesign->getCustomerSaveId().'/0.png');
                $newDesignsCollection[] = $mydesign->getData();
            }
        }
        else{
            $newDesignsCollection = null;
        }

        $jsonFactory->setData(['mydesigns'=>$newDesignsCollection]);
        return $jsonFactory;
    }

} 

