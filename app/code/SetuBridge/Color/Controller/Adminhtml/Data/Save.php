<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Color\Controller\Adminhtml\Data;

use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\Manager;
use Magento\Framework\Api\DataObjectHelper;
use SetuBridge\Color\Api\DataRepositoryInterface;
use SetuBridge\Color\Api\Data\DataInterface;
use SetuBridge\Color\Api\Data\DataInterfaceFactory;
use SetuBridge\Color\Controller\Adminhtml\Data;

class Save extends Data
{
    
    protected $messageManager;

    protected $dataRepository;

    protected $dataFactory;

    protected $dataObjectHelper;

    public function __construct(
        Registry $registry,
        DataRepositoryInterface $dataRepository,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Manager $messageManager,
        DataInterfaceFactory $dataFactory,
        DataObjectHelper $dataObjectHelper,
        Context $context
    ) {
        $this->messageManager   = $messageManager;
        $this->dataFactory      = $dataFactory;
        $this->dataRepository   = $dataRepository;
        $this->dataObjectHelper  = $dataObjectHelper;
        parent::__construct($registry, $dataRepository, $resultPageFactory, $resultForwardFactory, $context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data['sample_data_status'] = 0;
            $id = $this->getRequest()->getParam('color_id');
            if ($id) {
                $model = $this->dataRepository->getById($id);
            } else {
                unset($data['color_id']);
                $model = $this->dataFactory->create();
            }

            try {
                $this->dataObjectHelper->populateWithArray($model, $data, DataInterface::class);
                $this->dataRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Color has been successfully saved.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['color_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage("Attention: Something went wrong.");
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage("Attention: Something went wrong.");
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['color_id' => $this->getRequest()->getParam('color_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
