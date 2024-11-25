<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Controller\Adminhtml\Grid;

class Index extends \Magento\Backend\App\Action
{
    protected $_resultPageFactory;
    public $licence;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SetuBridge\Personalization\Model\Licence $licence,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->licence = $licence;
    }

    public function execute()
    {

        try {
            if($this->licence->isValidLicence()){
                $resultPage = $this->_resultPageFactory->create();
                $resultPage->setActiveMenu('SetuBridge_PersonalizationTemplate::personalization_template_list');
                $resultPage->getConfig()->getTitle()->prepend(__('Templates'));
                return $resultPage;
            }
            else{
                $this->messageManager->addError(__('Licence Key is Invalid'));
                $this->_redirect($this->_redirect->getRefererUrl());
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
