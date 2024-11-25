<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Clipart\Model\ResourceModel\Clipart;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{    
    protected $_idFieldName = 'clipart_id';
    protected $_eventPrefix = 'personalization_clipart_collection';
    protected $_eventObject = 'personalization_clipart_collection';

    protected function _construct()
    {
        $this->_init(
            'SetuBridge\Clipart\Model\Clipart',
            'SetuBridge\Clipart\Model\ResourceModel\Clipart'
        );
    }
}
