<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_OrderHistoryCommunication
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\OrderHistoryCommunication\Model\ResourceModel;
 
class OrderHistoryCommunication extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('order_history_communication', 'entity_id');
    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && ($field === null)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }
}