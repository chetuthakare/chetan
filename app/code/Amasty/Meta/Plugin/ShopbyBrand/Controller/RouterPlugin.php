<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Meta Tags Templates for Magento 2
 */

namespace Amasty\Meta\Plugin\ShopbyBrand\Controller;

use Amasty\Meta\Model\Registry;
use Amasty\ShopbyBrand\Controller\Router;
use Magento\Framework\App\ActionInterface;

class RouterPlugin
{
    public const IS_BRAND_PAGE = 'is_brand_page';

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function afterMatch(Router $subject, ?ActionInterface $result): ?ActionInterface
    {
        if ($result) {
            $this->registry->set(self::IS_BRAND_PAGE, true, true);
        }

        return $result;
    }
}
