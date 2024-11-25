<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Model;

class ProductEditpageData extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'product_editpage_data';

    protected $_cacheTag = 'product_editpage_data';

    protected $_eventPrefix = 'product_editpage_data';

    protected function _construct()
    {
        $this->_init('SetuBridge\PersonalizationTemplate\Model\ResourceModel\ProductEditpageData');
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