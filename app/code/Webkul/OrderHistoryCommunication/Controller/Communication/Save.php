<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_OrderHistoryCommunication
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\OrderHistoryCommunication\Controller\Communication;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunicationFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File;
use \Magento\Framework\Controller\ResultFactory;

class Save extends Action
{
    /**
     * @param Context $context
     * @param OrderHistoryCommunicationFactory $orderHistory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param Filesystem $filesystem
     * @param File $file
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Webkul\OrderHistoryCommunication\Helper\Data $data
     * @param \Magento\Framework\Controller\ResultFactory $result
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        OrderHistoryCommunicationFactory $orderHistory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem,
        File $file,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Webkul\OrderHistoryCommunication\Helper\Data $data,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->orderHistory = $orderHistory;
        $this->fileUploader = $fileUploaderFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->fileDriver = $fileDriver;
        $this->data = $data;
        $this->resultRedirect = $result;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        try {
            if ($this->customerSession->isLoggedIn()) {
                if ($this->getRequest()->isPost()) {
                    $files = $this->getRequest()->getFiles();
                    $post  = $this->getRequest()->getParams();
                    $notify = false;
                    $visible = false;
                    $orderHistory = $this->orderHistory->create();
                    $orderHistory->setComment($post['comment']);
                    $orderHistory->setOrderId($post['order_id']);
                    $orderHistory->setIsVisibleOnFront($visible);
                    $orderHistory->setIsCustomerNotified($notify);
                    $orderHistory->setIsCustomer(1);
                    if (!empty($files['attachment']['name'])) {

                        $path =
                        $this->data->getMediaPath()."orderhistorycommunication/communication/".$post['order_id'];
                        if (!$this->fileDriver->isExists($path)) {
                            $this->file->createDirectory($path);
                        }
                        $directory = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
                        $fileUploader = $this->fileUploader->create(['fileId' => 'attachment']);
                        $allowedExt = $this->data->getConfigValue(
                            "orderhistorycommunication/general_options/allowedextensions"
                        );
                        $allowedExt = explode(",", $allowedExt);
                        $fileUploader->setAllowedExtensions($allowedExt);
                        $fileUploader->setAllowRenameFiles(true);
                        $fileUploader->setFilesDispersion(false);
                        $result = $fileUploader->save($path);
                        $file = $result["file"];
                        $orderHistory->setAttachment($file);
                    }
                    $orderHistory->save();
                    $this->data->sendMail($orderHistory);
                    $this->messageManager->addSuccess(__("Order Communication has been saved !!"));
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    "customer/account/login",
                    [
                        '_secure' => $this->getRequest()->isSecure(),
                    ]
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        return $this->resultRedirectFactory->create()->setPath(
            "*/order/communication",
            [
                'order_id' => $this->getRequest()->getParam('order_id'),
                '_secure' => $this->getRequest()->isSecure(),
            ]
        );
    }
}
