<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\TemplateDesigns\Controller\Adminhtml\TemplateDesigns;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory; 

class Save extends \Magento\Backend\App\Action
{

    var $templatedesignsFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \SetuBridge\TemplateDesigns\Model\TemplateDesignsFactory $templatedesignsFactory
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->templatedesignsTemplateDesignsFactory = $templatedesignsFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->_redirect('templatedesigns/templatedesigns/addtemplatedesigns');
            return;
        }
        $data['sample_data_status'] = 0;
        try {
            $rowData = $this->templatedesignsTemplateDesignsFactory->create();
            if(isset($data['designs_image'])){
                $data['designs_image'] =   $data['designs_image']['value'];
            }
            $rowData->setData($data);

            if(!empty($this->getRequest()->getFiles('designs_image'))){
                $profileImage = $this->getRequest()->getFiles('designs_image');
                $fileName = ($profileImage && array_key_exists('name', $profileImage)) ? $profileImage['name'] : null;
                if ($profileImage && $fileName) {
                    $uploader = $this->uploaderFactory->create(['fileId' => 'designs_image']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                    $imageAdapterFactory = $this->adapterFactory->create();

                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

                    $result = $uploader->save($mediaDirectory->getAbsolutePath('Personalization/TemplateDesigns'));
                }
            }
            if(isset($result)){
                $image = 'Personalization/TemplateDesigns'.$result['file'];
                $rowData->setDesignsImage($image);
            }
            if ($this->getRequest()->getParam('id')) {

                $rowData->setDesignsId($this->getRequest()->getParam('id'));
            }

            $rowData->save();

            $this->messageManager->addSuccess(__('Designs has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Attention: Something went wrong."));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    protected function _isAllowed()
    {
        return true;
    }
}