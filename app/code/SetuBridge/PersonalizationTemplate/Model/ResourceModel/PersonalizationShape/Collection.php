<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Model\ResourceModel\PersonalizationShape;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'shape_id';
    protected $_eventPrefix = 'personalization_shape_collection';
    protected $_eventObject = 'personalization_shape_collection';

    protected function _construct()
    {
        $this->_init('SetuBridge\PersonalizationTemplate\Model\PersonalizationShape', 'SetuBridge\PersonalizationTemplate\Model\ResourceModel\PersonalizationShape');
    }

}