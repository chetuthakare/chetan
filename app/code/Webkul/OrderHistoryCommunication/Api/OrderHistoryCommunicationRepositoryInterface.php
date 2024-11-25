<?php
/**
 * Webkul Software.
 *
 * @category    Webkul
 * @package     Webkul_OrderHistoryCommunication
 * @author      Webkul
 * @copyright   Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license     https://store.webkul.com/license.html
 */

namespace Webkul\OrderHistoryCommunication\Api;

interface OrderHistoryCommunicationRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);
    /**
     * get by id
     *
     * @param int $id
     * @return \Webkul\OrderHistoryCommunication\Api\OrderHistoryCommunicationRepositoryInterface
     */
    public function getById($id);
    /**
     * get by id
     *
     * @param int $id
     * @return \Webkul\OrderHistoryCommunication\Api\OrderHistoryCommunicationRepositoryInterface
     */
    public function save(\Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication $subject);
    /**
     * delete
     *
     * @param \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication $subject
     * @return boolean
     */
    public function delete(\Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication $subject);
    /**
     * delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}
