<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Risecommerce\ContactForm\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Contact\Model\ConfigInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Filesystem\Driver\File;

class Post extends \Magento\Contact\Controller\Index implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param ConfigInterface $contactsConfig
     * @param DataPersistorInterface $dataPersistor
     * @param LoggerInterface $logger
     */
     
    protected $curlFactory;
    /**
     * @param Context $context
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param Filesystem $filesystem
     * @param File $file
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     */
    public function __construct(
        Context $context,
        ConfigInterface $contactsConfig,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        Filesystem $filesystem,
        File $file,
        CurlFactory $curlFactory,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
	\Risecommerce\FreshdeskOrderCommunications\Helper\Data $data,
        DataPersistorInterface $dataPersistor,
        LoggerInterface $logger = null
    ) {
    	$this->fileUploaderFactory = $fileUploaderFactory;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->curlFactory = $curlFactory;
        $this->fileDriver = $fileDriver;
        
        parent::__construct($context, $contactsConfig);
        $this->context = $context;
		$this->data = $data;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * Post user question
     *
     * @return Redirect
     */
    public function execute()
    {
      if ($this->getRequest()->isPost()){        
        
		$request = $this->getRequest();
		$files_arr = $_FILES['image'];
		$api_key = $this->data->getapikey();
                $password = $this->data->getapiPass();
                $domain = $this->data->getapidomain();
                $subject = $request->getParam('subject') ?? '';
		$description = $request->getParam('comment') ?? '';
		$email = $request->getParam('email') ?? '';
		$orderId =  '';
		$productName = '';
		$customerName = $request->getParam('name') ?? '';
		$phoneNumber = $request->getParam('telephone') ?? '';
		if (!empty($files_arr['name'])) {
                        $fname = $files_arr['name'];
                        $type = $files_arr['type'];
                        $size = $files_arr['size'];
                        $tmp_path = $files_arr['tmp_name'];
                        $error = $files_arr['error'];
                        $file_info = pathinfo($fname);
                        $extension = $file_info['extension'];
                        $filename = $file_info['filename'];
                        $image = "contact_" . time() . "." . $extension;
                        $_FILES['image']['name'] = $image;
                        // Image upload On Server
                        $mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
                        $mediapath = $this->mediaBaseDirectory = rtrim($mediaDir, '/');
                        $uploader = $this->fileUploaderFactory->create(['fileId' => 'image']);
                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                        $uploader->setAllowRenameFiles(true);
                        $path = $mediapath . '/Freshdesk/Contact/';
                        $result = $uploader->save($path);
                        //Uploded
                        $file_path = $path . $image;
                        $file = $image;
                        $ticket_payload = array(
                            'description' => $description,
                            'subject' => $subject,
                            'email' => $email,
                            'priority' => 1,
                            'status' => 2,
                            'custom_fields[cf_order_id]' => $orderId,
                            'custom_fields[cf_product_name]' => $productName,
                            'custom_fields[cf_customer_name]' => $customerName,
                            'custom_fields[cf_phone_number]' => $phoneNumber,
                            'attachments[]' => curl_file_create($file_path, "image/png", $file),
                        );
                        $headers[] = "Content-type: multipart/form-data;";
                    } else {
                        $ticket_payload = array(
                            'description' => $description,
                            'subject' => $subject,
                            'email' => $email,
                            'priority' => 1,
                            'status' => 2,
                            'custom_fields[cf_order_id]' => $orderId,
                            'custom_fields[cf_product_name]' => $productName,
                            'custom_fields[cf_customer_name]' => $customerName,
                            'custom_fields[cf_phone_number]' => $phoneNumber,
                          
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
                  
              
		$response = curl_exec($ch);	
                    
		if ($response === false) {
			$this->messageManager->addErrorMessage("Something went wrong");
		
		} else {
			
			$responseArray = json_decode($response, true);
			$errors = $responseArray['errors'] ?? '';
			if($errors){
				foreach ($errors as $error) {
					$field = $error['field'];
					$message = $error['message'];
					$code = $error['code'];
				}
			}
			if(isset($code) && $code == "invalid_value"){
				$this->messageManager->addErrorMessage("All the fields are required! ");					
			}else {
				$this->messageManager->addSuccessMessage(
				__('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.') );					
			}

			
		}
		curl_close($ch);
        return $this->resultRedirectFactory->create()->setPath('contact/index');
    }
    }
}
