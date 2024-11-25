<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package HTML Sitemap for Magento 2
 */

namespace Amasty\SeoHtmlSitemap\Helper;

use Amasty\Base\Model\Serializer;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public const CATEGORY_TREE_TYPE = 1;
    public const CATEGORY_LIST_TYPE = 2;
    public const PRODUCTS_COLUMNS_DEFAULT = 5;

    // General
    public const CONFIG_PAGE_TITLE_PATH                = 'amseohtmlsitemap/general/title';
    public const CONFIG_META_DESCRIPTION_PATH          = 'amseohtmlsitemap/general/meta_description';
    public const CONFIG_LAYOUT_PATH                    = 'amseohtmlsitemap/general/layout';
    public const CONFIG_SHOW_SEARCH_FIELD              = 'amseohtmlsitemap/general/show_search_field';
    public const CONFIG_SORT_ORDER                     = 'amseohtmlsitemap/general/sort_order';

    // Categories
    public const CONFIG_SHOW_CATEGORIES_PATH           = 'amseohtmlsitemap/categories/show_categories';
    public const CONFIG_EXCLUDE                        = 'amseohtmlsitemap/categories/exclude';
    public const CONFIG_CATEGORIES_TITLE               = 'amseohtmlsitemap/categories/categories_title';
    public const CONFIG_CATEGORIES_SHOW_AS             = 'amseohtmlsitemap/categories/show_as';
    public const CONFIG_CATEGORIES_COLUMN_NUMBER       = 'amseohtmlsitemap/categories/column_number';

    // Products
    public const CONFIG_SHOW_PRODUCTS_PATH             = 'amseohtmlsitemap/products/show_products';
    public const CONFIG_PRODUCTS_TITLE                 = 'amseohtmlsitemap/products/products_title';
    public const CONFIG_PRODUCTS_COLUMN_NUMBER         = 'amseohtmlsitemap/products/column_number';
    public const CONFIG_PRODUCTS_SPLIT_BY_LETTER       = 'amseohtmlsitemap/products/split_by_letter';
    public const CONFIG_PRODUCTS_HIDE_OUT_OF_STOCK     = 'amseohtmlsitemap/products/hide_out_of_stock';
    public const CONFIG_PRODUCTS_LIMIT                 = 'amseohtmlsitemap/products/maximum_limit';

    // CMS Pages
    public const CONFIG_SHOW_CMS_PAGES_PATH            = 'amseohtmlsitemap/cms/show_cms_pages';
    public const CONFIG_CMS_PAGES_TITLE                = 'amseohtmlsitemap/cms/cms_title';
    public const CONFIG_EXCLUDE_CMS_PAGES              = 'amseohtmlsitemap/cms/exclude_cms_pages';
    public const CONFIG_CMS_COLUMN_NUMBER              = 'amseohtmlsitemap/cms/column_number';
    public const CONFIG_EXCLUDE_CMS_PAGES_PATH         = 'amseohtmlsitemap/cms/exclude_cms_pages_values';

    // Landing Pages
    public const CONFIG_SHOW_LANDING_PATH              = 'amseohtmlsitemap/landing/show_landing_pages';
    public const CONFIG_LANDING_TITLE                  = 'amseohtmlsitemap/landing/landing_title';
    public const CONFIG_LANDING_COLUMN_NUMBER          = 'amseohtmlsitemap/landing/column_number';

    // Additional links
    public const CONFIG_LINKS_PATH                     = 'amseohtmlsitemap/additional/additional_links';
    public const CONFIG_LINKS_TITLE                    = 'amseohtmlsitemap/additional/links_title';
    public const CONFIG_LINKS_COLUMN_NUMBER            = 'amseohtmlsitemap/additional/column_number';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $defaultPositions = [
        'categories' => 'Categories',
        'products' => 'Products',
        'cms_pages' => 'CMS pages',
        'links' => 'Links'
    ];

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    public function __construct(
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->serializer = $serializer;
        $this->escaper = $escaper;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getScopeValue($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->escaper->escapeHtml($data, $allowedTags);
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return trim((string)$this->getScopeValue(self::CONFIG_PAGE_TITLE_PATH));
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return trim((string)$this->getScopeValue(self::CONFIG_META_DESCRIPTION_PATH));
    }

    /**
     * @return mixed
     */
    public function getLayout()
    {
        return $this->getScopeValue(self::CONFIG_LAYOUT_PATH);
    }

    /**
     * @return bool
     */
    public function canShowSearchField()
    {
        return (bool)$this->getScopeValue(self::CONFIG_SHOW_SEARCH_FIELD);
    }

    /**
     * @return array
     */
    public function getSortOrder()
    {
        $value = $this->getScopeValue(self::CONFIG_SORT_ORDER);
        if ($value) {
            $value = $this->serializer->unserialize($value);
        } else {
            $value = $this->getDefaultPositions();
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getCMSHeaderTitle()
    {
        return trim((string)$this->getScopeValue(self::CONFIG_CMS_PAGES_TITLE));
    }

    /**
     * @return int
     */
    public function getCMSNumberOfColumns()
    {
        return (int)$this->getScopeValue(self::CONFIG_CMS_COLUMN_NUMBER);
    }

    /**
     * @return bool
     */
    public function canShowCMSPages()
    {
        return (bool)$this->getScopeValue(self::CONFIG_SHOW_CMS_PAGES_PATH);
    }

    /**
     * @return mixed
     */
    public function getExcludeCMSPages()
    {
        $cmsPages = $this->getScopeValue(self::CONFIG_EXCLUDE_CMS_PAGES_PATH);
        return $cmsPages;
    }

    /**
     * @return string
     */
    public function getCategoriesTitle()
    {
        return trim((string)$this->getScopeValue(self::CONFIG_CATEGORIES_TITLE));
    }

    /**
     * @return bool
     */
    public function canShowCategories()
    {
        return (bool)$this->getScopeValue(self::CONFIG_SHOW_CATEGORIES_PATH);
    }

    /**
     * @return int
     */
    public function getCategoriesShowAs()
    {
        return (int)$this->getScopeValue(self::CONFIG_CATEGORIES_SHOW_AS);
    }

    /**
     * @return string
     */
    public function getExcludeCategoryIds()
    {
        return $this->getScopeValue(self::CONFIG_EXCLUDE);
    }

    /**
     * @return int
     */
    public function getCategoriesNumberOfColumns()
    {
        return (int)$this->getScopeValue(self::CONFIG_CATEGORIES_COLUMN_NUMBER);
    }

    /**
     * @return string
     */
    public function getProductsTitle()
    {
        return trim((string)$this->getScopeValue(self::CONFIG_PRODUCTS_TITLE));
    }

    /**
     * @return bool
     */
    public function canSnowProducts()
    {
        return (bool)$this->getScopeValue(self::CONFIG_SHOW_PRODUCTS_PATH);
    }

    /**
     * @return int
     */
    public function getProductsNumberOfColumns()
    {
        return (int)$this->getScopeValue(self::CONFIG_PRODUCTS_COLUMN_NUMBER) ?: self::PRODUCTS_COLUMNS_DEFAULT;
    }

    /**
     * @return bool
     */
    public function getProductsSplitByLetter()
    {
        return (bool)$this->getScopeValue(self::CONFIG_PRODUCTS_SPLIT_BY_LETTER);
    }

    /**
     * @return bool
     */
    public function getProductsHideOutOfStock()
    {
        return (bool)$this->getScopeValue(self::CONFIG_PRODUCTS_HIDE_OUT_OF_STOCK);
    }

    /**
     * @return int
     */
    public function getProductsLimit()
    {
        return (int)$this->getScopeValue(self::CONFIG_PRODUCTS_LIMIT);
    }

    /**
     * @return string
     */
    public function getLandingTitle()
    {
        return trim((string)$this->getScopeValue(self::CONFIG_LANDING_TITLE));
    }

    /**
     * @return bool
     */
    public function canShowLandingPages()
    {
        return (bool)$this->getScopeValue(self::CONFIG_SHOW_LANDING_PATH);
    }

    /**
     * @return int
     */
    public function getLandingNumberOfColumns()
    {
        return (int)$this->getScopeValue(self::CONFIG_LANDING_COLUMN_NUMBER);
    }

    /**
     * @return string
     */
    public function getLinksTitle()
    {
        return trim((string)$this->getScopeValue(self::CONFIG_LINKS_TITLE));
    }

    /**
     * @return string
     */
    public function getAdditionalLinks()
    {
        return (string)$this->getScopeValue(self::CONFIG_LINKS_PATH);
    }

    /**
     * @return int
     */
    public function getLinksNumberOfColumns()
    {
        return (int)$this->getScopeValue(self::CONFIG_LINKS_COLUMN_NUMBER);
    }

    /**
     * @return array
     */
    private function getDefaultPositions()
    {
        $defaultPositions = [];
        foreach ($this->defaultPositions as $key => $position) {
            $defaultPositions[$key] = __($position);
        }

        return $defaultPositions;
    }
}
