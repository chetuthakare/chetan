<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Controller\Adminhtml\Font;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{

    var $fontFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationFontFactory $fontFactory
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->personalizationFontFactory = $fontFactory;
    }

    public function execute()
    {

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->_redirect('personalization/shape/addshape');
            return;
        }
        try {
            $data['sample_data_status'] = 0;
            $rowData = $this->personalizationFontFactory->create();
            $rowData->setData($data);

            if(!empty($this->getRequest()->getFiles('font_file'))){
                $profileImage = $this->getRequest()->getFiles('font_file');
                $fileName = ($profileImage && array_key_exists('name', $profileImage)) ? $profileImage['name'] : null;
                if ($profileImage && $fileName) {
                    $uploader = $this->uploaderFactory->create(['fileId' => 'font_file']);
                    $uploader->setAllowedExtensions(['otf','ttf','eot','woff','woff2','fon','fnt']);

                    $imageAdapterFactory = $this->adapterFactory->create();

                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

                    $result = $uploader->save($mediaDirectory->getAbsolutePath('Personalization/Font'));
                }
            }
            if(isset($result)){
                $image = 'Personalization/Font'.$result['file'];
                $rowData->setFontFile($image);
            }
            $id = $this->getRequest()->getParam('id');
            if (isset($id)) {
                $rowData->setFontId($this->getRequest()->getParam('id'));
            }
            $rowData->save();

            $this->messageManager->addSuccess(__('Font has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Attention: Something went wrong."));
        }
        $this->_redirect('personalization/font/index');
    }


    protected function _isAllowed()
    {
        return true;
    }
}