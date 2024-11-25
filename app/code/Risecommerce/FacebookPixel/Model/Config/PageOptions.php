<?php
/**
 * Class PageOptions
 *
 * PHP version 7 & 8
 *
 * @category Risecommerce
 * @package  Risecommerce_FacebookPixel
 * @author   Risecommerce <magento@risecommerce.com>
 * @license  https://www.risecommerce.com  Open Software License (OSL 3.0)
 * @link     https://www.risecommerce.com
 */
namespace Risecommerce\FacebookPixel\Model\Config;


class PageOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'registration_page', 'label' => 'Registration Page'],
            ['value' => 'cms_page', 'label' => 'Cms Page'],
            ['value' => 'search_page', 'label' => 'Search Page'],
            ['value' => 'advanced_search_page', 'label' => 'Advanced Search Page'],
            ['value' => 'category_page', 'label' => 'Category Page'],
            ['value' => 'product_page', 'label' => 'Product Page'],
            ['value' => 'checkout_page', 'label' => 'Checkout Page'],
            ['value' => 'success_page', 'label' => 'Success Page'],
            ['value' => 'account_page', 'label' => 'Account Page']
        ];
    }
}
