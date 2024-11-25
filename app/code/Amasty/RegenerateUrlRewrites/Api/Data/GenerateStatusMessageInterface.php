<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package URL Rewrites Regenerator for Magento 2
 */

namespace Amasty\RegenerateUrlRewrites\Api\Data;

interface GenerateStatusMessageInterface
{
    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @param int $type
     * @return \Amasty\RegenerateUrlRewrites\Api\Data\GenerateStatusMessageInterface
     */
    public function setType(int $type): GenerateStatusMessageInterface;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return \Amasty\RegenerateUrlRewrites\Api\Data\GenerateStatusMessageInterface
     */
    public function setMessage(string $message): GenerateStatusMessageInterface;
}
