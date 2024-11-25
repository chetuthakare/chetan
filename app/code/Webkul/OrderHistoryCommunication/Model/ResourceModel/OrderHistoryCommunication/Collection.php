<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_OrderHistoryCommunication
 * @author    Webkul
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\OrderHistoryCommunication\Model\ResourceModel\OrderHistoryCommunication;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \Webkul\OrderHistoryCommunication\Api\Data\OrderResultInterface;

/**
 * Webkul Helpdesk ResourceModel Tickets collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication::class,
            \Webkul\OrderHistoryCommunication\Model\ResourceModel\OrderHistoryCommunication::class
        );
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
}
