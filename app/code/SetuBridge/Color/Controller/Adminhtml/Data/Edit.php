<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Color\Controller\Adminhtml\Data;

use SetuBridge\Color\Controller\Adminhtml\Data;

class Edit extends Data
{
    
    public function execute()
    {
        $dataId = $this->getRequest()->getParam('color_id');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SetuBridge_Color::data')
            ->addBreadcrumb(__('Data'), __('Data'))
            ->addBreadcrumb(__('Manage Data'), __('Manage Data'));

        if ($dataId === null) {
            $resultPage->addBreadcrumb(__('New Color'), __('New Color'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Color'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Color'), __('Edit Color'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->dataRepository->getById($dataId)->getColorName()
            );
        }
        return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
