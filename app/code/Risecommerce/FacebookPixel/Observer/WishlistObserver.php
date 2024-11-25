<?php
/**
 * Class WishlistObserver
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_FacebookPixel
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\FacebookPixel\Observer;

use Magento\Framework\Event\ObserverInterface;


class WishlistObserver implements ObserverInterface
{

    /**
     * SessionManager
     *
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $session;

    /**
     * Helper
     *
     * @var \Risecommerce\FacebookPixel\Helper\Data
     */
    protected $helper;

    /**
     * WishlistObserver constructor.
     *
     * @param \Magento\Framework\Session\SessionManager $session
     * @param \Risecommerce\FacebookPixel\Helper\Data         $helper
     */
    public function __construct(
        \Magento\Framework\Session\SessionManager $session,
        \Risecommerce\FacebookPixel\Helper\Data $helper
    ) {
        $this->session = $session;
        $this->helper = $helper;
    }

    /**
     * Observer event
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return bool|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getItem()->getProduct();
        if (!$this->helper->isAddToWishList() || !$product) {
            return true;
        }

        $data = [
            'content_type' => 'product',
            'content_ids' => $product->getSku(),
            'content_name' => $product->getName(),
            'currency' => $this->helper->getCurrencyCode(),
            'price' => $product->getFinalPrice(),
        ];

        $this->session->setData('add_to_wishlist', $data);
        return true;
    }
}
