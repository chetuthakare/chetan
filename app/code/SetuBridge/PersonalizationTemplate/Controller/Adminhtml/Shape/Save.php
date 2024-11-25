<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Controller\Adminhtml\Shape;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{

    var $personalizationFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationShapeFactory $shapeFactory
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->personalizationShapeFactory = $shapeFactory;
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
            $rowData = $this->personalizationShapeFactory->create();
            $rowData->setData($data);

            if(!empty($this->getRequest()->getFiles('shape_image'))){
                $profileImage = $this->getRequest()->getFiles('shape_image');
                $fileName = ($profileImage && array_key_exists('name', $profileImage)) ? $profileImage['name'] : null;
                if ($profileImage && $fileName) {
                    $uploader = $this->uploaderFactory->create(['fileId' => 'shape_image']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);

                    $imageAdapterFactory = $this->adapterFactory->create();

                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

                    $result = $uploader->save($mediaDirectory->getAbsolutePath('Personalization/Shape'));
                }
            }
            if(isset($result)){
                $image = 'Personalization/Shape'.$result['file'];
                $rowData->setShapeImage($image);
            }
            if (isset($data['shape_id'])) {
                $rowData->setShapeId($data['shape_id']);
            }
            $rowData->save();

            $this->messageManager->addSuccess(__('Pattern has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Attention: Something went wrong."));
        }

        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('personalization/shape/addshape', ['_current' => true, 'id' => $rowData->getShapeId(), 'active_tab' => '']);
        }
        $this->_redirect('personalization/shape/index');
    }

    protected function _isAllowed()
    {
        return true;
    }
}