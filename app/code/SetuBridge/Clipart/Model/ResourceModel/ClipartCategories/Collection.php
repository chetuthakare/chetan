<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Clipart\Model\ResourceModel\ClipartCategories;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $_idFieldName = 'clipartcategories_id';
    protected $_eventPrefix = 'personalization_clipartcategories_collection';
    protected $_eventObject = 'personalization_clipartcategories_collection';
    
    protected function _construct()
    {
        $this->_init(
            'SetuBridge\Clipart\Model\ClipartCategories',
            'SetuBridge\Clipart\Model\ResourceModel\ClipartCategories'
        );
    }
}
