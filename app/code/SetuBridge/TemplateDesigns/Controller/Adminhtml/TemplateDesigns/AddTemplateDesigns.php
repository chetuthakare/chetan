<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\TemplateDesigns\Controller\Adminhtml\TemplateDesigns;

use Magento\Framework\Controller\ResultFactory;

class AddTemplateDesigns extends \Magento\Backend\App\Action
{

    private $coreRegistry;

    private $gridFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \SetuBridge\TemplateDesigns\Model\TemplateDesignsFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->gridFactory = $gridFactory;
    }

    public function execute()
    {
        
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->gridFactory->create();
    
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
            if (!$rowData->getDesignsId()) {
                $this->messageManager->addError(__('TemplateDesigns no longer exist.'));
                $this->_redirect('templatedesigns/templatedesigns/index');
                return;
            }
        }
        
        $this->coreRegistry->register('templatedesigns_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Designs ').$rowTitle : __('Add Designs ');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return true;
    }
}