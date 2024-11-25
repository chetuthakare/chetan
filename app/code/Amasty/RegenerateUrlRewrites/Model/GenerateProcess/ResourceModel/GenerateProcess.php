<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package URL Rewrites Regenerator for Magento 2
 */

namespace Amasty\RegenerateUrlRewrites\Model\GenerateProcess\ResourceModel;

use Amasty\RegenerateUrlRewrites\Model\GenerateProcess\GenerateProcess as GenerateProcessModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GenerateProcess extends AbstractDb
{
    public const TABLE_NAME = 'amasty_regenerate_url_rewrites_process';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, GenerateProcessModel::ID);
    }
}
