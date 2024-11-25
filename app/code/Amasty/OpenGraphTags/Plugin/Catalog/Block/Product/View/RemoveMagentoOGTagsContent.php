<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Open Graph Tags for Magento 2 (System)
 */

namespace Amasty\OpenGraphTags\Plugin\Catalog\Block\Product\View;

use Amasty\OpenGraphTags\Model\ConfigProvider;
use Magento\Catalog\Block\Product\View;

class RemoveMagentoOGTagsContent
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    
    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @see View::toHtml()
     *
     * @param View $subject
     * @param string $html
     * @return string
     */
    public function afterToHtml(View $subject, string $html): string
    {
        if ($subject->getNameInLayout() === 'opengraph.general' && $this->configProvider->isEnabledOnProductPage()) {
            $html = '';
        }

        return $html;
    }
}
