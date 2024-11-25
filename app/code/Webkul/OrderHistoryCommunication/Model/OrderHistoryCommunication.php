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

namespace Webkul\OrderHistoryCommunication\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface;

/**
 * OrderHistoryCommunication Model.
 *
 * @method \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication
 */
class OrderHistoryCommunication extends AbstractModel implements IdentityInterface, OrderHistoryCommunicationInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Order History Communication cache tag.
     */
    const CACHE_TAG = 'order_history_communication';

    /**
     * @var string
     */
    protected $_cacheTag = 'order_history_communication';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'order_history_communication';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\OrderHistoryCommunication\Model\ResourceModel\OrderHistoryCommunication::class
        );
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteSaleslist();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Saleslist.
     *
     * @return \Webkul\Marketplace\Model\Saleslist
     */
    public function noRouteSaleslist()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Set orderId
     *
     * @param int $orderId
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }
    /**
     * Get orderId
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }
    /**
     * Set IS_CUSTOMER_NOTIFIED
     *
     * @param boolean $isCustomerNotified
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setIsCustomerNotified($isCustomerNotified)
    {
        return $this->setData(self::IS_CUSTOMER_NOTIFIED, $isCustomerNotified);
    }
    /**
     * Get IS_CUSTOMER_NOTIFIED
     *
     * @return boolean
     */
    public function getIsCustomerNotified()
    {
        return $this->getData(self::IS_CUSTOMER_NOTIFIED);
    }
    /**
     * Set isVisibleOnFront
     *
     * @param int $isVisibleOnFront
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setIsVisibleOnFront($isVisibleOnFront)
    {
        return $this->setData(self::IS_VISIBLE_ON_FRONT, $isVisibleOnFront);
    }
    /**
     * Get isVisibleOnFront
     *
     * @return boolean
     */
    public function getIsVisibleOnFront()
    {
        return $this->getData(self::IS_VISIBLE_ON_FRONT);
    }
    /**
     * Set comment
     *
     * @param string $comment
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }
    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }
    /**
     * Set CREATED_AT
     *
     * @param  string $createdAt
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
    /**
     * Get CREATED_AT
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }
    /**
     * Set Attachment
     *
     * @param string $attachment
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setAttachement($attachment)
    {
        return $this->setData(self::ATTACHMENT, $attachment);
    }
    /**
     * Get Attachment
     *
     * @return string
     */
    public function getAttachement()
    {
        return $this->getData(self::ATTACHMENT);
    }
    /**
     * Set Is Customer
     *
     * @param boolean $isCustomer
     * @return Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterface
     */
    public function setIsCustomer($isCustomer)
    {
        return $this->setData(self::IS_CUSTOMER, $isCustomer);
    }
    /**
     * Get Is Customer
     *
     * @return boolean
     */
    public function getIsCustomer()
    {
        return $this->getData(self::IS_CUSTOMER);
    }
}
