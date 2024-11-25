<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Color\Controller\Adminhtml\Data;

use SetuBridge\Color\Controller\Adminhtml\Data;

class Index extends Data
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Colors'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
