<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Color\Model\ResourceModel\Data;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    
    protected $_idFieldName = 'color_id';


    protected function _construct()
    {
        $this->_init('SetuBridge\Color\Model\Data', 'SetuBridge\Color\Model\ResourceModel\Data');
    }
}
