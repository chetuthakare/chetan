<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php
namespace SetuBridge\Clipart\Model;

class ClipartCategories extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    
    const CACHE_TAG = 'personalization_clipartcategories';

    protected $_cacheTag = 'personalization_clipartcategories';

    protected $_eventPrefix = 'personalization_clipartcategories';

    protected function _construct()
    {
        $this->_init('SetuBridge\Clipart\Model\ResourceModel\ClipartCategories');
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
