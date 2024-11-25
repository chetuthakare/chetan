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

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{    
    protected $_idFieldName = 'customer_save_id';
    protected $_eventPrefix = 'personalization_save_customer_product_collection';
    protected $_eventObject = 'personalization_save_customer_product_collection';

    protected function _construct()
    {
        $this->_init(
            'SetuBridge\Personalization\Model\SaveCustomerProduct',
            'SetuBridge\Personalization\Model\ResourceModel\SaveCustomerProduct'
        );
    }
}
