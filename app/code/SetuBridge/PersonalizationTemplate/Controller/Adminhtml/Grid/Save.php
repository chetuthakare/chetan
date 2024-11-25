<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\PersonalizationTemplate\Controller\Adminhtml\Grid;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\Action
{

    var $personalization;
    public $adapterFactory;
    public $uploaderFactory;
    public $filesystem;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplate $personalization
    ) {
        parent::__construct($context);
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->personalization = $personalization;
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPostValue();

        try {

            if ($data['unique_id']) {
                $this->personalization->load($data['unique_id'], 'unique_id');

                if ($this->personalization->getEntityId()) {
                    $this->personalization->setIsActive($data['is_active']);
                    $this->personalization->setTitle($data['title']);
                } else {
                    $this->personalization->setIsActive($data['is_active']);
                    $this->personalization->setTitle($data['title']);
                }
            } else {
                if (isset($data['entity_id'])) {
                    $this->personalization->load($data['entity_id'], 'entity_id');

                    if ($this->personalization->getEntityId()) {
                        $this->personalization->setIsActive($data['is_active']);
                        $this->personalization->setTitle($data['title']);
                    } else {
                        $this->personalization->setIsActive($data['is_active']);
                        $this->personalization->setTitle($data['title']);
                    }
                } else {
                    $this->personalization->setIsActive($data['is_active']);
                    $this->personalization->setTitle($data['title']);
                }
            }
            $this->personalization->setData('unique_id', '');

            $saveData = $this->personalization->save();

            if ($saveData) {
                $this->messageManager->addSuccess(__('Template has been successfully saved.'));
            } else {
                $this->messageManager->addError(__('Some Error.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Attention: Something went wrong."));
        }

        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('personalization/grid/addrow', ['_current' => true, 'id' => $saveData->getEntityId(), 'active_tab' => '']);
        }

        $this->_redirect('personalization/grid/index');
    }

    protected function _isAllowed()
    {
        return true;
    }
}
