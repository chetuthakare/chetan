<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Clipart\Controller\Adminhtml\ClipartCategories;

use Magento\Framework\Controller\ResultFactory;

class AddClipartCategories extends \Magento\Backend\App\Action
{

    private $coreRegistry;

    private $clipartcategoriesFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \SetuBridge\Clipart\Model\ClipartCategoriesFactory $clipartcategoriesFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->clipartcategoriesFactory = $clipartcategoriesFactory;
    }

    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->clipartcategoriesFactory->create();
    
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
            if (!$rowData->getClipartcategoriesId()) {
                $this->messageManager->addError(__('ClipartCategories data no longer exist.'));
                $this->_redirect('clipart/clipartcategories/index');
                return;
            }
        }

        $this->coreRegistry->register('clipartcategories_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit ClipartCategory ').$rowTitle : __('Add Clipart Category');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
