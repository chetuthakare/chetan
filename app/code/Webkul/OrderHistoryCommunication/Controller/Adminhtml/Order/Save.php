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

namespace Webkul\OrderHistoryCommunication\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;

class Save extends \Magento\Sales\Controller\Adminhtml\Order implements HttpPostActionInterface
{
     /**
      * @var \Magento\Framework\View\Result\PageFactory
      */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunicationFactory $orderHistory
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     * @param \Webkul\OrderHistoryCommunication\Helper\Data $data
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param Filesystem $filesystem
     * @param File $file
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Request\Http $request,
        \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunicationFactory $orderHistory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        \Webkul\OrderHistoryCommunication\Helper\Data $data,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem,
        File $file,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );
        $this->data = $data;
        $this->request = $request;
        $this->orderHistory = $orderHistory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->fileUploader = $fileUploaderFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->fileDriver = $fileDriver;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @return \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function execute()
    {
        try {
            $order = $this->_initOrder();
            $data = $this->getRequest()->getPost('comment');
            $files = $this->getRequest()->getFiles();
            if (isset($data['comment']) && empty($data['comment'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The comment is missing. Enter and try again.')
                );
            }
            $order_id = $data['order_id'];
            $notify = $data['is_customer_notified'];
            $visible = $data['is_visible_on_front'];

            $comment = trim(strip_tags($data['comment']));
            $orderHistoryId = "";
            $orderHistory = $this->orderHistory->create();
            $orderHistory->setOrderId($order_id);
            $orderHistory->setComment($comment);
            $orderHistory->setIsVisibleOnFront($visible);
            $orderHistory->setIsCustomerNotified($notify);
            $orderHistory->setIsCustomer(0);
            if (!empty($files['file']['name'])) {
                $path =
                $this->data->getMediaPath()."orderhistorycommunication/communication/".$order_id;
                if (!$this->fileDriver->isExists($path)) {
                    $this->file->createDirectory($path);
                }
                $directory = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
                $fileUploader = $this->fileUploader->create(['fileId' => 'file']);
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
            $orderHistoryId = $orderHistory->getId();
            if ($notify == 1) {
                $this->data->sendAdminMail($orderHistory);
            }
            $resultJson = $this->resultJsonFactory->create();
            $response = ['error' => false, 'message' => __('success')];
            $resultJson->setData($response);
            return $resultJson;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __($e->getMessage())];
        }
        if (is_array($response)) {
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        }
    }
}
