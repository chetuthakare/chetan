<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Block\Customer;

class MyDesign extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \SetuBridge\Personalization\Model\SaveCustomerProductFactory $saveCustomerProductFactory,
        \Magento\Customer\Model\SessionFactory $customerSession
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->saveCustomerProductFactory = $saveCustomerProductFactory;
    }
    public function getCustomerDesigns(){

        if($this->_customerSession->create()->isLoggedIn()){
            $customerId = $this->_customerSession->create()->getCustomerData()->getId();
            $itemsCollection = $this->saveCustomerProductFactory->create()->getCollection()->addFieldToFilter('customer_id',$customerId)->setOrder('customer_save_id', 'desc');

            $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
            
            $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 5;

            $itemsCollection->setPageSize($pageSize);
            $itemsCollection->setCurPage($page);
            return  $itemsCollection;
        }
        return false;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCustomerDesigns()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'fme.news.pager'
            )->setAvailableLimit(array(5=>5,10=>10,15=>15))->setShowPerPage(true)->setCollection(
                $this->getCustomerDesigns()
            );
            $this->setChild('pager', $pager);
            $this->getCustomerDesigns()->load();
        }
        return $this;
    }
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}

