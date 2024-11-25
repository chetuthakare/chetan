<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Model\ResourceModel;

class ProductEditpageData extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    )
    {
        parent::__construct($context, $resourcePrefix);
    }
    
    protected function _construct()
    {
        $this->_init('product_editpage_data', 'entity_id');
    }
    
}