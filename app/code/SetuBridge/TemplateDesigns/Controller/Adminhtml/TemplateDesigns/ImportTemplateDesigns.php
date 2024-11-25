<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\TemplateDesigns\Controller\Adminhtml\TemplateDesigns;

class ImportTemplateDesigns extends \Magento\Backend\App\Action
{
    
    protected $_resultPageFactory;
 
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) 
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }
 
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('SetuBridge_TemplateDesigns::personalization_importtemplatedesigns_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Import TemplateDesigns'));
 
        return $resultPage;
    }
 
    protected function _isAllowed()
    {
        return true;
    }
}