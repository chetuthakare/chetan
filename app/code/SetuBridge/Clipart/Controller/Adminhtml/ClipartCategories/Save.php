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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{

    var $clipartcategoriesFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SetuBridge\Clipart\Model\ClipartCategoriesFactory $clipartcategoriesFactory
    ) {
        parent::__construct($context);
        $this->clipartcategoriesFactory = $clipartcategoriesFactory;
    }

    public function execute()
    {
        
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->_redirect('clipart/ClipartCategories/addrow');
            return;
        }
        try {
            $rowData = $this->clipartcategoriesFactory->create();
            $rowData->setData($data);
        
            $id = $this->getRequest()->getParam('id');
            if (isset($id)) { 
                $rowData->setClipartcategoriesId($id);
            }

            $rowData->save();
            $this->messageManager->addSuccess(__('Clipart category has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Attention: Something went wrong."));
        }
        $this->_redirect('clipart/clipartcategories/index');
    }

    protected function _isAllowed()
    {
        return true;
    }
}
