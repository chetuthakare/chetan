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

/**
 * Interface for preorder Complete search results.
 * @api
 */
interface OrderResultInterface extends SearchResultsInterface
{
    /**
     * Get sellerlist Complete list.
     *
     * @return \Webkul\Marketplace\Api\Data\SellerInterface[]
     */
    public function getItems();

    /**
     * Set sellerlist Complete list.
     *
     * @param \Webkul\Marketplace\Api\Data\SellerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
