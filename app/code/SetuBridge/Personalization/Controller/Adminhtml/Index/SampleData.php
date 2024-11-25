<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class SampleData extends \Magento\Backend\App\Action
{

    const CSV_FILE ="personalization_clipart.csv";

    const TABLE_NAME ="personalization_clipart";
    
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \SetuBridge\Personalization\Helper\Data $helperData,
        \SetuBridge\Clipart\Controller\Adminhtml\Clipart\ImportSave $saveClipart
    ) {
        parent::__construct($context);
        $this->helperData = $helperData;  
        $this->saveClipart = $saveClipart;  
        $this->filesystem = $filesystem;    
    }

    public function execute()
    {

        try {            
            
            $this->helperData->deleteOldSampleData(self::TABLE_NAME);

            $this->getRequest()->setPostValue('file_directory',"pub/media/Personalization/import/cliparts");

            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);            

            $resultPath = $mediaDirectory->getAbsolutePath('Personalization/import/csv/');

            $this->getRequest()->setPostValue('resultPath',$resultPath);

            $this->getRequest()->setPostValue('CsvName',sprintf(self::CSV_FILE));

            $this->_redirect('personalization/clipart/importsave');

            $this->saveClipart->execute(); 


        } catch (\Exception $e) {

            $this->messageManager->addError(__("Attention: Something went wrong."));

            return $this->_redirect($this->_redirect->getRefererUrl());

        }
    }

    public function _isAllowed()
    {
        return true;
    }
}