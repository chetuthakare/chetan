<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SEO Toolkit Base for Magento 2
 */

namespace Amasty\SeoToolkitLite\Api\Data;

interface RedirectInterface
{
    public const TABLE_NAME = 'amasty_seotoolkit_redirect';
    public const STORE_TABLE_NAME = 'amasty_seotoolkit_redirect_store';
    public const REDIRECT_ID = 'redirect_id';
    public const STATUS = 'status';
    public const REQUEST_PATH = 'request_path';
    public const TARGET_PATH = 'target_path';
    public const IS_TARGET_PATH_EXTERNAL = 'is_target_path_external';
    public const REDIRECT_TYPE = 'redirect_type';
    public const UNDEFINED_PAGE_ONLY = 'undefined_page_only';
    public const PRIORITY = 'priority';
    public const DESCRIPTION = 'description';
    public const STORE_ID = 'store_id';
    public const IS_AUTOGENERATED = 'is_autogenerated';
    
    public const AUTOGENERATED_ENABLED = 1;

    /**
     * @return int
     */
    public function getRedirectId(): int;

    /**
     * @param int $redirectId
     *
     * @return \Amasty\SeoToolkitLite\Api\Data\RedirectInterface
     */
    public function setRedirectId($redirectId);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\SeoToolkitLite\Api\Data\RedirectInterface
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getRequestPath();

    /**
     * @param string|null $requestPath
     *
     * @return \Amasty\SeoToolkitLite\Api\Data\RedirectInterface
     */
    public function setRequestPath($requestPath);

    /**
     * @return string|null
     */
    public function getTargetPath();

    /**
     * @param string|null $targetPath
     *
     * @return \Amasty\SeoToolkitLite\Api\Data\RedirectInterface
     */
    public function setTargetPath($targetPath);

    /**
     * @return int
     */
    public function getRedirectType();

    /**
     * @param int $redirectType
     *
     * @return \Amasty\SeoToolkitLite\Api\Data\RedirectInterface
     */
    public function setRedirectType($redirectType);

    /**
     * @return int
     */
    public function getUndefinedPageOnly();

    /**
     * @param int $undefinedPageOnly
     *
     * @return \Amasty\SeoToolkitLite\Api\Data\RedirectInterface
     */
    public function setUndefinedPageOnly($undefinedPageOnly);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     *
     * @return \Amasty\SeoToolkitLite\Api\Data\RedirectInterface
     */
    public function setPriority($priority);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param string|null $description
     *
     * @return \Amasty\SeoToolkitLite\Api\Data\RedirectInterface
     */
    public function setDescription($description);

    /**
     * @return bool
     */
    public function getIsTargetPathExternal(): bool;

    /**
     * @param bool $isTargetPathExternal
     * @return void
     */
    public function setIsTargetPathExternal(bool $isTargetPathExternal): void;

    /**
     * @return bool
     */
    public function getIsAutogenerated(): bool;

    /**
     * @param bool $isAutogenerated
     * @return void
     */
    public function setIsAutogenerated(bool $isAutogenerated): void;
}