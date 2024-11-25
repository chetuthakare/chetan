<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Controller\Adminhtml\Grid;

use Magento\Framework\Controller\ResultFactory;
 
class AddRow extends \Magento\Backend\App\Action
{
    
    private $coreRegistry;
 
    private $personalizationFactory;
 
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplateFactory $personalizationFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->personalizationFactory = $personalizationFactory;
    }
 
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->personalizationFactory->create();
    
        if ($rowId) {
           $rowData = $rowData->load($rowId);
           $rowTitle = $rowData->getTitle();
           if (!$rowData->getEntityId()) {
               $this->messageManager->addError(__('template no longer exist.'));
               $this->_redirect('personalization/grid/rowdata');
               return;
           }
       }
 
       $this->coreRegistry->register('row_data', $rowData);
       $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
       $title = $rowId ? __('Edit ').$rowTitle : __('Add Template');
       $resultPage->getConfig()->getTitle()->prepend($title);
       return $resultPage;
    }
 
    protected function _isAllowed()
    {
        return true;
    }
}