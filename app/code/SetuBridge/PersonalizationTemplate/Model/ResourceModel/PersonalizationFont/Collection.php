<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Model\ResourceModel\PersonalizationFont;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'font_id';
    protected $_eventPrefix = 'personalization_font_collection';
    protected $_eventObject = 'personalization_font_collection';
    protected function _construct()
    {
        $this->_init('SetuBridge\PersonalizationTemplate\Model\PersonalizationFont', 'SetuBridge\PersonalizationTemplate\Model\ResourceModel\PersonalizationFont');
    }

}