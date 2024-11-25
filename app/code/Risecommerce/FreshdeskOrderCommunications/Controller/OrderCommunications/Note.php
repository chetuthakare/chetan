<?php
namespace Risecommerce\FreshdeskOrderCommunications\Controller\OrderCommunications;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Filesystem\Driver\File;
use \Magento\Framework\Controller\ResultFactory;

class Note extends Action
{
    protected $curlFactory;
    /**
     * @param Context $context
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param Filesystem $filesystem
     * @param File $file
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\Controller\ResultFactory $result
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem,
        File $file,
        CurlFactory $curlFactory,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Risecommerce\FreshdeskOrderCommunications\Helper\Data $data,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->curlFactory = $curlFactory;
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
                    $post = $this->getRequest()->getParams();
                    $user_id = (int)$post['user_id'];
                    $ticketId = $post['ticket_id'];
                    $comment = $post['comment'];
                    $files_arr = $_FILES['image'];
                    $api_key = $this->data->getapikey();
                    $password = $this->data->getapiPass();
                    $domain = $this->data->getapidomain();
                    $apiEndpoint = "https://$domain.freshdesk.com/api/v2/tickets/{$ticketId}";
                    $headers = array(
                        'Content-Type: application/json',
                    );
                    $ch = curl_init($apiEndpoint);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_USERPWD, "$api_key:X");
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if ($httpCode === 200) {
                        $ticketData = json_decode($response, true);
                        $customFields = $ticketData['custom_fields'];
                        if (!empty($files_arr['name'])) {
                            $fname = $files_arr['name'];
                            $type = $files_arr['type'];
                            $size = $files_arr['size'];
                            $tmp_path = $files_arr['tmp_name'];
                            $error = $files_arr['error'];
                            $file_info = pathinfo($fname);
                            $extension = $file_info['extension'];
                            $filename = $file_info['filename'];
                            $image = $ticketId . "reply_" . time() . "." . $extension;
                            $_FILES['image']['name'] = $image;
                            // Image upload On Server
                            $mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
                            $mediapath = $this->mediaBaseDirectory = rtrim($mediaDir, '/');
                            $uploader = $this->fileUploaderFactory->create(['fileId' => 'image']);
                            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                            $uploader->setAllowRenameFiles(true);
                            $path = $mediapath . '/Freshdesk/';
                            $result = $uploader->save($path);
                            $attachment_path = $path . $image;
                            //End Image Uploding
                            $noteData = array(
                                "body" => $comment,
                                "user_id" => $user_id,
                                'attachments[]' =>  curl_file_create($attachment_path, "image/png", $image)
                            );
                        } else {
                            $noteData = array(
                                "body" => $comment,
                                "user_id" => $user_id,
                            );
                        }
                        $url = "https://pushplinen.freshdesk.com/api/v2/tickets/{$ticketId}/notes";
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HEADER, true);
                        curl_setopt($ch, CURLOPT_USERPWD, "$api_key:$password");
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $noteData);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $server_output = curl_exec($ch);
                        $info = curl_getinfo($ch);
                        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                        $headers = substr($server_output, 0, $header_size);
                        $response = substr($server_output, $header_size);
                        if ($info['http_code'] == 201) {
                            $this->messageManager->addSuccess(__('Reply Added Successfully! '));
                        } else {
                            $this->messageManager->addSuccess(__('Unable to Add Try Once Again!'));
                        }
                    } else {
                        echo "Error   outer: " . $httpCode;
                    }
                    curl_close($ch);
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
            "*/ordercommunications/index",
            [
                'order_id' => $this->getRequest()->getParam('order_id'),
                '_secure' => $this->getRequest()->isSecure(),
            ]
        );
    }
}

