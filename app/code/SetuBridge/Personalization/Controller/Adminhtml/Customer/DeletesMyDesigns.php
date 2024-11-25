<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Adminhtml\Customer;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use SetuBridge\Personalization\Model\ResourceModel\SaveCustomerProduct\CollectionFactory;

class DeletesMyDesigns extends \Magento\Backend\App\Action
{
    protected $_filter;

    protected $_collectionFactory;
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \SetuBridge\Personalization\Model\ImageProcessor $imageProcessor
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->imageProcessor =$imageProcessor;
        parent::__construct($context);
    }
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $deleteIds = $params['selected'];
        $collection = $this->_collectionFactory->create()->addFieldToFilter('customer_save_id',$deleteIds);

        if(!empty($deleteIds) && $collection->getSize() > 0){
            foreach ($collection->getItems() as $record) { 
                $this->imageProcessor->removeDesignFileDirectory($record->getCustomerSaveId());
                $record->delete();
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collection->getSize()));
        }
        else{
            $this->messageManager->addError(__('No one find record find.'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('customer/index/edit',['id'=>$params['id']]);
    }

    protected function _isAllowed()
    {
        return true;
    }
}