<?php
declare(strict_types=1);

namespace Swissup\Pagespeed\Model\Image\Custom;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImageGenerator extends \Swissup\Pagespeed\Model\Image\AbstractGenerator
{
    /**
     * Storage collection factory
     *
     * @var \Swissup\Pagespeed\Model\Image\Custom\Storage\CollectionFactory
     */
    private $storageCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $staticDirectory;

    /**
     * @var \Swissup\Pagespeed\Helper\Config
     */
    private $configHelper;

    /**
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Swissup\Pagespeed\Model\Image\Custom\Storage\CollectionFactory $storageCollectionFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Swissup\Pagespeed\Helper\Config $configHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Swissup\Pagespeed\Model\Image\Custom\Storage\CollectionFactory $storageCollectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Swissup\Pagespeed\Helper\Config $configHelper
    ) {
        parent::__construct($cache, $cacheState);
        $this->storageCollectionFactory = $storageCollectionFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->staticDirectory = $filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $this->configHelper = $configHelper;
    }

    /**
     * @return string
     */
    private function getFilenamesFilter()
    {
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif'];
        $filenameFilter = $this->filenameFilter ? "^.*{$this->filenameFilter}.*" : '' /*'^[a-z0-9\-\_]+'*/;
        $filesFilter = "/{$filenameFilter}\.(" . implode('|', $allowedExtensions) . ')$/i';

        return $filesFilter;
    }

    /**
     * @return \Swissup\Pagespeed\Model\Image\Custom\Storage\Collection
     * @throws \Exception
     */
    private function getMediaRootFileCollection()
    {
        $collection = $this->storageCollectionFactory->create();
        $targetDir = '.';
        $path = $this->mediaDirectory->getAbsolutePath($targetDir);
        if ($path !== null && $this->mediaDirectory->isDirectory($path)) {
            $filesFilter = $this->getFilenamesFilter();
            $collection->addTargetDir($path);
            $collection
                ->setCollectDirs(false)
                ->setCollectFiles(true)
                ->setCollectRecursively(false)
                ->setFilesFilter($filesFilter)
                ->setOrder('mtime', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        }

        return $collection;
    }

    /**
     * @return bool
     */
    private function isStaticFrontendDirExist()
    {
        $targetDir = 'frontend';
        $path = $this->staticDirectory->getAbsolutePath($targetDir);

        return $path !== null && $this->staticDirectory->isDirectory($path);
    }

    /**
     * @return \Swissup\Pagespeed\Model\Image\Custom\Storage\Collection
     * @throws \Exception
     */
    private function getStaticThemeFileCollection()
    {
        $collection = $this->storageCollectionFactory->create();
        $targetDir = 'frontend';
        $path = $this->staticDirectory->getAbsolutePath($targetDir);
        if ($path !== null && $this->staticDirectory->isDirectory($path)) {
            $filesFilter = $this->getFilenamesFilter();
            $collection->addTargetDir($path);
            $collection
                ->setCollectDirs(false)
                ->setCollectFiles(true)
                ->setCollectRecursively(true)
                ->setFilesFilter($filesFilter)
                ->setDirsFilter('/^(?!.*(0\.5x|0\.75x|2x|3x).*$)(.*)/i')
                ->setOrder('mtime', \Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ;
        }

        return $collection;
    }

    /**
     * @param \Swissup\Pagespeed\Model\Image\Custom\Storage\Collection $collection
     * @param \Swissup\Pagespeed\Model\Image\Custom\Storage\Collection $additionalCollection
     * @return \Swissup\Pagespeed\Model\Image\Custom\Storage\Collection
     * @throws \Exception
     */
    private function mergeCollection(
        \Swissup\Pagespeed\Model\Image\Custom\Storage\Collection $collection,
        \Swissup\Pagespeed\Model\Image\Custom\Storage\Collection $additionalCollection
    ) {
        $additionalCollectionSize = $additionalCollection->getSize();
        if ($additionalCollectionSize > 0) {
            $lastId = $collection->getLastItem()->getId();
            foreach ($additionalCollection as $item) {
                $lastId += 1;
                $item->setId($lastId);
                $collection->addItem($item);
            }
            $collection->inreaseSize($additionalCollectionSize);
        }

        return $collection;
    }

    /**
     * @return \Swissup\Pagespeed\Model\Image\Custom\Storage\Collection
     * @throws \Exception
     */
    private function getCollection()
    {
        $filesFilter = $this->getFilenamesFilter();
        /** @var \Swissup\Pagespeed\Model\Image\Custom\Storage\Collection $collection */
        $collection = $this->storageCollectionFactory->create();

        $targetDirs = $this->configHelper->getResizeCommandTargetDirs();
        foreach ($targetDirs as $targetDir) {
            $path = $this->mediaDirectory->getAbsolutePath($targetDir);
            if ($path !== null && $this->mediaDirectory->isDirectory($path)) {
                $collection->addTargetDir($path);
            }
        }

        $collection
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(true)
            ->setDirsFilter('/^(?!.*(0\.5x|0\.75x|2x|3x).*$)(.*)/i')
            ->setFilesFilter($filesFilter)
            ->setOrder('mtime',\Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $collection->load();

        try {
            $mediaRootFileCollection = $this->getMediaRootFileCollection();
            $mediaRootFileCollection->load();
            $collection = $this->mergeCollection($collection, $mediaRootFileCollection);
        } catch (\Magento\Framework\Exception\ValidatorException $e) {
        }

        if ($this->isStaticFrontendDirExist()) {
            try {
                $staticThemeFileCollection = $this->getStaticThemeFileCollection();
                $staticThemeFileCollection->load();
                $collection = $this->mergeCollection($collection, $staticThemeFileCollection);
            } catch (\Magento\Framework\Exception\ValidatorException $e) {
            }
        }

        return $collection;
    }

    /**
     * @return \Generator
     * @throws \Exception
     */
    public function create(): \Generator
    {
        $collection = $this->getCollection();
        $i = 0;
        $page = $this->loadCurPage() ?: 0;
        $pageSize = $this->pageSize;
        $hasResults = false;
        foreach ($collection as $item) {
            $i++;
            if ($i <= $page * $pageSize) {
                continue;
            }
            if ($i > ($page + 1) * $pageSize) {
                break;
            }
            $key = $item['id'];
            $value = [
                'filename' => $item['basename'],
                'path' => $item['filename']
            ];

            $hasResults = true;
            yield $key => $value;
        }
        $page++;
        if (!$hasResults) {
            $page = null;
        }
        $this->saveCurPage($page);
    }
}
