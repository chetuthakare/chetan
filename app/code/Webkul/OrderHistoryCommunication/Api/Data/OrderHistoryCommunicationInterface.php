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

namespace Webkul\OrderHistoryCommunication\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderHistoryCommunicationInterface
{

    const ENTITY_ID = 'entity_id';

    const ORDER_ID = 'order_id';

    const IS_CUSTOMER_NOTIFIED = 'is_customer_notified';

    const IS_VISIBLE_ON_FRONT = 'is_visible_on_front';

    const COMMENT = 'comment';

    const CREATED_AT = 'created_at';

    const ATTACHMENT = 'attachment';

    const IS_CUSTOMER = 'is_customer';

    /**
     * Set EntityId
     *
     * @param int $entityId
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setEntityId($entityId);
    /**
     * Get EntityId
     *
     * @return int
     */
    public function getEntityId();
    /**
     * Set orderId
     *
     * @param int $orderId
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setOrderId($orderId);
    /**
     * Get orderId
     *
     * @return int
     */
    public function getOrderId();
    /**
     * Set IS_CUSTOMER_NOTIFIED
     *
     * @param boolean $isCustomerNotified
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setIsCustomerNotified($isCustomerNotified);
    /**
     * Get IS_CUSTOMER_NOTIFIED
     *
     * @return boolean
     */
    public function getIsCustomerNotified();
    /**
     * Set isVisibleOnFront
     *
     * @param int $isVisibleOnFront
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setIsVisibleOnFront($isVisibleOnFront);
    /**
     * Get isVisibleOnFront
     *
     * @return boolean
     */
    public function getIsVisibleOnFront();
    /**
     * Set comment
     *
     * @param string $comment
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setComment($comment);
    /**
     * Get comment
     *
     * @return string
     */
    public function getComment();
    /**
     * Set CREATED_AT
     *
     * @param  string $createdAt
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get CREATED_AT
     *
     * @return string
     */
    public function getCreatedAt();
    /**
     * Set Attachment
     *
     * @param string $attachment
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setAttachement($attachment);
    /**
     * Get Attachment
     *
     * @return string
     */
    public function getAttachement();
    /**
     * Set Is Customer
     *
     * @param boolean $isCustomer
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setIsCustomer($isCustomer);
    /**
     * Get Is Customer
     *
     * @return boolean
     */
    public function getIsCustomer();
}
