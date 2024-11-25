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

class Index extends \Magento\Backend\App\Action
{
    
    private $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SetuBridge_Clipart::clipartcategories_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Clipart Categories'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
