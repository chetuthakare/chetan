<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\TemplateDesigns\Model;

class TemplateDesigns extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'personalization_templatedesigns';

    protected $_cacheTag = 'personalization_templatedesigns';

    protected $_eventPrefix = 'personalization_templatedesigns';

    protected function _construct()
    {
        $this->_init('SetuBridge\TemplateDesigns\Model\ResourceModel\TemplateDesigns');
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