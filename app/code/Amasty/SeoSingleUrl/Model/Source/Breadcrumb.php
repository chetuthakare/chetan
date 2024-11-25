<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Unique Product URL for Magento 2
 */

namespace Amasty\SeoSingleUrl\Model\Source;

class Breadcrumb implements \Magento\Framework\Option\ArrayInterface
{
    const CURRENT_URL = 'current_url';

    const LAST_VISITED = 'last_visited';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CURRENT_URL,
                'label' => __('Current Url(Default Magento Breadcrumbs)')
            ],
            [
                'value' => self::LAST_VISITED,
                'label' => __('Last Visited Category')
            ]
        ];
    }
}
