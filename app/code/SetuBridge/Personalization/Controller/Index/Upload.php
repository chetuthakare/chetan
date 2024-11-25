<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\Personalization\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends \Magento\Framework\App\Action\Action
{

    public $adapterFactory;
    public $uploaderFactory;
    public $filesystem;
    public $storeManagerInterface;

    public function __construct(
        Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    public function execute()
    {
        $jsonFactory = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {

            //$uploadedName = $this->getRequest()->getPostValue('file_name');
            $folderPath = $this->getRequest()->getPostValue('folder_path');
            if (!empty($this->getRequest()->getFiles('file'))) {

                $profileImage = $this->getRequest()->getFiles('file');

                $fileName = ($profileImage && array_key_exists('name', $profileImage)) ? $profileImage['name'] : null;

                if ($profileImage && $fileName) {

                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);

                    //  $profileImage['name'] = $uploadedName.'.'.$ext;

                    $uploader = $this->uploaderFactory->create(['fileId' => $profileImage]);

                    $uploader->setAllowRenameFiles(true);

                    $uploader->setAllowedExtensions(['svg', 'png', 'jpeg', 'jpg']);
                    $imageAdapterFactory = $this->adapterFactory->create();

                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

                    $result = $uploader->save($mediaDirectory->getAbsolutePath('Personalization/productimage/' . $folderPath));
                }
            }
            if (isset($result)) {
                $image = 'Personalization/productimage/' . $folderPath . '/' . $result['file'];
                $mediaUrl = $this->storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $jsonFactory->setData(['imageUrl' => $mediaUrl . $image, 'error' => false]);
                return $jsonFactory;
            } else {
                $jsonFactory->setData(['error' => true, 'message' => "Something went to wrong"]);
                return $jsonFactory;
            }
        } catch (\Exception $e) {
            $jsonFactory->setData(['error' => true, 'message' => $e->getMessage()]);
            return $jsonFactory;
        }
    }
}
