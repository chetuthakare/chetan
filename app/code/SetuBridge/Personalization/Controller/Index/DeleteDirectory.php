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

class DeleteDirectory extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $file
    ) 
    {
        parent::__construct($context);
        $this->personalizeProduct =$personalizeProduct;
        $this->_filesystem = $filesystem;
        $this->_file = $file;
    }

    public function execute()
    {    
        $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {

            $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
            $folderPath = $this->getRequest()->getParam('folder_path');
            $path = $mediapath.$folderPath;

            if ($this->_file->isExists($path))  {

                $this->_file->deleteFile($path);

            }

            $jsonFactory->setData(['error' => false,'message'=>"Deleted Successfully."]);
            return $jsonFactory;

        } catch (\Exception $e) {
            $jsonFactory->setData(['error' => true,'message'=>$e->getMessage()]);
            return $jsonFactory;
        }  
    }
} 

