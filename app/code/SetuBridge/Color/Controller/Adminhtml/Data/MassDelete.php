<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Color\Controller\Adminhtml\Data;

use SetuBridge\Color\Model\Data;

class MassDelete extends MassAction
{

    protected function massAction(Data $data)
    {
        $this->dataRepository->delete($data);
        return $this;
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
