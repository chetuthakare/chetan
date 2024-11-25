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

class Clipart extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    
    const CACHE_TAG = 'personalization_clipart';

    protected $_cacheTag = 'personalization_clipart';

    protected $_eventPrefix = 'personalization_clipart';

    protected function _construct()
    {
        $this->_init('SetuBridge\Clipart\Model\ResourceModel\Clipart');
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
