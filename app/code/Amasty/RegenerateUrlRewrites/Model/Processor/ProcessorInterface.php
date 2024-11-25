<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package URL Rewrites Regenerator for Magento 2
 */

namespace Amasty\RegenerateUrlRewrites\Model\Processor;

use Generator;

interface ProcessorInterface
{
    /**
     * @param int $storeId
     * @param array $entityIds
     * @return Generator
     */
    public function process(int $storeId, array $entityIds): Generator;
}
