<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class TemplateDesignSampleData extends \Magento\Backend\App\Action
{

    const CSV_FILE ="personalization_templatedesigns.csv";


    public function __construct(
        Context $context,
        \SetuBridge\Personalization\Helper\Data $helperData,
        \SetuBridge\TemplateDesigns\Controller\Adminhtml\TemplateDesigns\ImportSave $importSave,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context); 
        $this->filesystem = $filesystem; 
        $this->helperData = $helperData;  
        $this->importSave = $importSave;  
    }

    public function execute()
    {
        try {

            $this->helperData->deleteOldSampleData('personalization_templatedesigns');

            $this->getRequest()->setPostValue('file_directory',"pub/media/Personalization/import/templatedesigns");

            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);            

            $resultPath = $mediaDirectory->getAbsolutePath('Personalization/import/csv/');

            $this->getRequest()->setPostValue('resultPath',$resultPath);

            $this->getRequest()->setPostValue('CsvName',sprintf(self::CSV_FILE));

            $this->_redirect('templatedesigns/templatedesigns/importsave');

            $result = $this->importSave->execute();

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