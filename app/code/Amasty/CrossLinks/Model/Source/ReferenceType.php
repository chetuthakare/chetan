<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Cross Linking for Magento 2
 */

namespace Amasty\CrossLinks\Model\Source;

class ReferenceType implements \Magento\Framework\Option\ArrayInterface
{
    public const REFERENCE_TYPE_CUSTOM   = 0;
    public const REFERENCE_TYPE_PRODUCT  = 1;
    public const REFERENCE_TYPE_CATEGORY = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::REFERENCE_TYPE_CUSTOM, 'label' => __('Custom Url')],
            ['value' => self::REFERENCE_TYPE_PRODUCT, 'label' => __('Product Id')],
            ['value' => self::REFERENCE_TYPE_CATEGORY, 'label' => __('Category Id')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::REFERENCE_TYPE_CUSTOM => __('Custom Url'),
            self::REFERENCE_TYPE_PRODUCT => __('Product Id'),
            self::REFERENCE_TYPE_CATEGORY => __('Category Id')
        ];
    }
}
