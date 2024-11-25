<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Personalization\Model;

class SaveCustomerProduct extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    
    const CACHE_TAG = 'personalization_save_customer_product';

    protected $_cacheTag = 'personalization_save_customer_product';

    protected $_eventPrefix = 'personalization_save_customer_product';

    protected function _construct()
    {
        $this->_init('SetuBridge\Personalization\Model\ResourceModel\SaveCustomerProduct');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }    
}
