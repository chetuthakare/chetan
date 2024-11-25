<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Model;

class PersonalizationShape extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'personalization_shape';

    protected $_cacheTag = 'personalization_shape';

    protected $_eventPrefix = 'personalization_shape';

    protected function _construct()
    {
        $this->_init('SetuBridge\PersonalizationTemplate\Model\ResourceModel\PersonalizationShape');
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