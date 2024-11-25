<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Controller\Adminhtml\Font;

use Magento\Framework\Controller\ResultFactory;

class AddFont extends \Magento\Backend\App\Action
{
    
    private $coreRegistry;

    private $fontFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationFontFactory $fontFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->fontFactory = $fontFactory;
    }

    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->fontFactory->create();

        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
            if (!$rowData->getFontId()) {
                $this->messageManager->addError(__('Font no longer exist.'));
                $this->_redirect('personalizationTemplate/font/index');
                return;
            }
        }
        
        $this->coreRegistry->register('font_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Font').$rowTitle : __('Add Font');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return true;
    }
}