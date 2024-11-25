<?php
declare(strict_types=1);

namespace Swissup\Pagespeed\Api;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
interface ServiceImageGeneratorInterface
{
    /**
     * @return \Generator
     * @throws \Exception
     */
    public function create(): \Generator;

    /**
     *
     * @param string $filename
     */
    public function setFilenameFilter($filename);

    /**
     *
     * @param int $pageSize
     */
    public function setPageSize($pageSize);

}
