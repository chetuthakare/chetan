<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Clipart\Controller\Adminhtml\Clipart;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{

    var $clipartFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \SetuBridge\Clipart\Model\ClipartFactory $clipartFactory
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->clipartFactory = $clipartFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->_redirect('clipart/clipart/addrow');
            return;
        }
        $data['sample_data_status'] = 0;
        try {
            $rowData = $this->clipartFactory->create();
            $rowData->setData($data);
            if(!empty($this->getRequest()->getFiles('clipart_image'))){
                $profileImage = $this->getRequest()->getFiles('clipart_image');

                $fileName = ($profileImage && array_key_exists('name', $profileImage)) ? $profileImage['name'] : null;  
                if ($profileImage && $fileName) {
                    $uploader = $this->uploaderFactory->create(['fileId' => 'clipart_image']);

                    $uploader->setAllowedExtensions(['svg','png','jpeg','jpg']);
                    $imageAdapterFactory = $this->adapterFactory->create();

                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);

                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

                    $result = $uploader->save($mediaDirectory->getAbsolutePath('Personalization/Clipart'));
                }

            }   
            if(isset($result)){
                $image = 'Personalization/Clipart'.$result['file'];
                $rowData->setClipartImage($image);
            }
            $id = $this->getRequest()->getParam('id');

            if (isset($id)) {

                $rowData->setClipartId($id);
            }

            $rowData->save();
            $this->messageManager->addSuccess(__('Clipart has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Attention: Something went wrong."));
        }
        $this->_redirect('clipart/clipart/index');
    }

    protected function _isAllowed()
    {
        return true;
    }
}
