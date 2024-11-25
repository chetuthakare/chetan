<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class ColorSampleData extends \Magento\Backend\App\Action
{

    const CSV_FILE ="personalization_color.csv";
    
    const TABLE_NAME ="personalization_color";


    public function __construct(
        Context $context,
        \SetuBridge\Personalization\Helper\Data $helperData,
        \SetuBridge\Color\Controller\Adminhtml\Data\ImportSave $importSave,
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

            $this->helperData->deleteOldSampleData(self::TABLE_NAME);

            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);            

            $resultPath = $mediaDirectory->getAbsolutePath('Personalization/import/csv/');

            $this->getRequest()->setPostValue('resultPath',$resultPath);

            $this->getRequest()->setPostValue('CsvName',sprintf(self::CSV_FILE));

            $this->_redirect('color/data/importsave');

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