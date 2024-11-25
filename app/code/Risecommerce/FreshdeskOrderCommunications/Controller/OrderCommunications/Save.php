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

class Save extends Action
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
                    $order_id = $post['orderid'];
                    $customer_name = $post['name'];
                    $email = $post['email'];
                    $phone = $post['phonenumber'];
                    $product_names = $post['productitems'];
                    $product_name = implode(", ", $product_names);
                    $comment = $post['comment'];
                    $api_key = $this->data->getapikey();
                    $password = $this->data->getapiPass();
                    $domain = $this->data->getapidomain();
                    $files_arr = $_FILES['image'];
                    $subject = "Support needed for order Id : " . $order_id;
                    if (!empty($files_arr['name'])) {
                        $fname = $files_arr['name'];
                        $type = $files_arr['type'];
                        $size = $files_arr['size'];
                        $tmp_path = $files_arr['tmp_name'];
                        $error = $files_arr['error'];
                        $file_info = pathinfo($fname);
                        $extension = $file_info['extension'];
                        $filename = $file_info['filename'];
                        $image = $order_id . "_" . time() . "." . $extension;
                        $_FILES['image']['name'] = $image;
                        // Image upload On Server
                        $mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
                        $mediapath = $this->mediaBaseDirectory = rtrim($mediaDir, '/');
                        $uploader = $this->fileUploaderFactory->create(['fileId' => 'image']);
                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                        $uploader->setAllowRenameFiles(true);
                        $path = $mediapath . '/Freshdesk/';
                        $result = $uploader->save($path);
                        //Uploded
                        $file_path = $path . $image;
                        $file = $image;
                        $ticket_payload = array(
                            'description' => $comment,
                            'subject' => $subject,
                            'email' => $email,
                            'priority' => 1,
                            'status' => 2,
                            'custom_fields[cf_order_id]' => $order_id,
                            'custom_fields[cf_product_name]' => $product_name,
                            'custom_fields[cf_customer_name]' => $customer_name,
                            'custom_fields[cf_phone_number]' => $phone,
                            'custom_fields[cf_email]' => $email,
                            'attachments[]' => curl_file_create($file_path, "image/png", $file),
                        );
                        $headers[] = "Content-type: multipart/form-data;";
                    } else {
                        $ticket_payload = array(
                            'description' => $comment,
                            'subject' => $subject,
                            'email' => $email,
                            'priority' => 1,
                            'status' => 2,
                            'custom_fields[cf_order_id]' => $order_id,
                            'custom_fields[cf_product_name]' => $product_name,
                            'custom_fields[cf_customer_name]' => $customer_name,
                            'custom_fields[cf_phone_number]' => $phone,
                            'custom_fields[cf_email]' => $email,
                        );
                        $headers[] = "Content-type: multipart/form-data;";
                    }
                    $url = "https://$domain.freshdesk.com/api/v2/tickets";
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_USERPWD, "$api_key");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $ticket_payload);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $server_output = curl_exec($ch);
                    $info = curl_getinfo($ch);
                    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                    $headers = substr($server_output, 0, $header_size);
                    $response = substr($server_output, $header_size);
                    //END
                    if ($info['http_code'] == 201) {
                        $this->createOnFreshdesk($domain, $api_key, $customer_name, $email, $phone);
                        $this->messageManager->addSuccess(__('Ticket Created Successfully!'));
                    } else {
                        if ($info['http_code'] == 404) {
                            echo "Error, Please check the end point \n";
                        } else {
                            echo "Error, HTTP Status Code : " . $info['http_code'] . "\n";
                        }
                        curl_close($ch);
                    }
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
//create User on Freshdesk
    public function createOnFreshdesk($domain, $api_Key, $customer_name, $email, $phone)
    {
        $baseUrl = "https://$domain.freshdesk.com/api/v2";
        $endpoint = "/contacts";
        $apiKey = $api_Key;
        $customerData = array(
            "name" => $customer_name,
            "email" => $email,
            "phone" =>  $phone
        );
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode($apiKey . ":X")
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customerData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 201) {
            echo "Customer created successfully";
        } else {
            echo "Failed to create customer. Response: " . $response;
        }
    }
}
