<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\TemplateDesigns\Model\ResourceModel\TemplateDesigns;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'designs_id';
    protected $_eventPrefix = 'personalization_templatedesigns_collection';
    protected $_eventObject = 'personalization_templatedesigns_collection';

    protected function _construct()
    {
        $this->_init('SetuBridge\TemplateDesigns\Model\TemplateDesigns', 'SetuBridge\TemplateDesigns\Model\ResourceModel\TemplateDesigns');
    }

}