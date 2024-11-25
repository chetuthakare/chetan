<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Model\ResourceModel\PersonalizationTemplate;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'personalization_data_collection';
    protected $_eventObject = 'personalization_collection';

    protected function _construct()
    {
        $this->_init('SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplate', 'SetuBridge\PersonalizationTemplate\Model\ResourceModel\PersonalizationTemplate');
    }

}