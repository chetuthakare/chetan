<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Plugin\Store\Model;

use Amasty\XmlSitemap\Api\SitemapInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;

class StoreMakeUrlSecure
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function afterIsCurrentlySecure(
        Store $subject,
        $isSecure
    ) {
        if ($this->registry->registry(SitemapInterface::SITEMAP_GENERATION) && $subject->isUrlSecure()) {
            $isSecure = true;
        }

        return $isSecure;
    }
}
