<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\Personalization\Controller\Index;

class DownloadZip extends \Magento\Framework\App\Action\Action
{
    protected $zipArchive;
    protected $directoryList;
    protected $driver;
    protected $fileFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Shell\Driver $driver,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Archive\Zip $zip
    ) {
        parent::__construct($context);
        $this->zipArchive = $zip;
        $this->directoryList = $directoryList;
        $this->driver = $driver;
        $this->fileFactory = $fileFactory;
    }
    public function execute()
    {
        try {
            if (!class_exists('\ZipArchive')) {
                $this->messageManager->addError(__('ZipArchive class not found.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setRefererOrBaseUrl();
                return $resultRedirect;
            }
            $itemId = $this->getRequest()->getParam('itemId');
            $orderId = $this->getRequest()->getParam('orderId');
            $dir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $rootPath = $dir . '/Personalization/SourceCode/Order/' . $orderId . '/' . $itemId . '/FullImages';
            if ($this->isFileExist($rootPath)) {
                chdir($rootPath);
                $zip = new \ZipArchive();
                $zip->open($itemId . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($rootPath),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($rootPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }

                $zip->close();

                $this->fileFactory->create(
                    $itemId . '.zip',
                    [
                        'type' => 'filename',
                        'value' =>  $rootPath . '/' . $itemId . '.zip',
                        'rm' => true
                    ],
                    \Magento\Framework\App\Filesystem\DirectoryList::ROOT,
                    'application/zip'
                );
            } else {
                $this->messageManager->addError(__('Some issues face in images to download.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setRefererOrBaseUrl();
                return $resultRedirect;
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Attention: Something went wrong."));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }
    }

    private function isFileExist($rootPath)
    {
        return file_exists($rootPath);
    }
}
