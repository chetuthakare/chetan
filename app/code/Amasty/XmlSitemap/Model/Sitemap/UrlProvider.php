<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package XML Google® Sitemap for Magento 2
 */

namespace Amasty\XmlSitemap\Model\Sitemap;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;

class UrlProvider
{
    public const MEDIA_PART = 'pub/media/';
    public const PUB_PART = 'pub/';

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        File $ioFile,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        $this->ioFile = $ioFile;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
    }

    public function getSitemapUrl(string $filePath, int $storeId): ?string
    {
        if ($filePath && $this->isFileExists($filePath)) {
            $url = $this->getCorrectUrl($filePath, $storeId);
        }

        return $url ?? null;
    }

    public function getRobotsSitemapUrl(string $filePath, int $storeId): ?string
    {
        if ($filePath && !$this->isFileExists($filePath)) {
            $filePath = $this->getIndexFilePath($filePath);

            if (!$this->isFileExists($filePath)) {
                return null;
            }
        }

        return $this->getCorrectUrl($filePath, $storeId);
    }

    private function isFileExists(string $filePath): bool
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath() . $filePath;

        return $this->ioFile->fileExists($path);
    }

    private function getCorrectUrl(string $filePath, int $storeId): string
    {
        $store = $this->storeManager->getStore($storeId);
        if (strpos($filePath, self::MEDIA_PART) !== false) {
            $filePath = $this->generateSpecificSitemapUrl($filePath, $store, self::MEDIA_PART);
        } elseif (strpos($filePath, self::PUB_PART) !== false) {
            $filePath = $this->generateSpecificSitemapUrl($filePath, $store, self::PUB_PART);
        } else {
            $baseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_DIRECT_LINK);
            $filePath = $baseUrl . $filePath;
        }

        return $filePath;
    }

    private function generateSpecificSitemapUrl(string $filePath, StoreInterface $store, string $pathType): string
    {
        $urlType = $pathType == self::MEDIA_PART ? UrlInterface::URL_TYPE_MEDIA : UrlInterface::URL_TYPE_DIRECT_LINK;

        $baseUrl = $store->getBaseUrl($urlType);
        $filePath = str_replace($pathType, '', $filePath);
        $filePath = ltrim($filePath, '/');

        return $baseUrl . $filePath;
    }

    private function getIndexFilePath(string $filePath): string
    {
        return str_replace('.xml', '_index.xml', $filePath);
    }
}
