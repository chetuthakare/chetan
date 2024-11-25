<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Color\Controller\Adminhtml\Data;

use SetuBridge\Color\Controller\Adminhtml\Data;

class Add extends Data
{
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
