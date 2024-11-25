<?php
namespace Risecommerce\FreshdeskOrderCommunications\Controller\Adminhtml\OrderCommunications;

class Reply extends \Risecommerce\FreshdeskOrderCommunications\Controller\Adminhtml\Addreply
{
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
			print_r($data);
			die;
        }
        return $resultRedirect->setPath('*/*/');
    }
}
?>