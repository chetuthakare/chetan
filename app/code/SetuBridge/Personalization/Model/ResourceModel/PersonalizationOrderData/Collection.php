<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Personalization\Model\ResourceModel\SaveCustomerProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    
    protected $_idFieldName = 'entity_id';


    protected function _construct()
    {
        $this->_init('SetuBridge\Personalization\Model\PersonalizationOrderData', 'SetuBridge\Personalization\Model\ResourceModel\PersonalizationOrderData');
    }
}
