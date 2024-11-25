<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SEO Toolkit Base for Magento 2
 */

namespace Amasty\SeoToolkitLite\Model\Redirect\Product;

use Amasty\SeoToolkitLite\Model\Category\PathGetter;
use Amasty\SeoToolkitLite\Model\ConfigProvider;
use Amasty\SeoToolkitLite\Model\Redirect\CreateRedirect;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogUrlRewrite\Model\Map\UrlRewriteFinder;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Framework\App\ObjectManager;

/**
 * Takes existing redirects before deletion and creates new ones in our table
 */
class ProcessBeforeDeletion
{
    /**
     * @var UrlRewriteFinder
     */
    private $urlRewriteFinder;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CreateRedirect
     */
    private $createRedirect;

    /**
     * @var PathGetter
     */
    private $categoryPathGetter;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductUrlPathGenerator
     */
    private $productPathGenerator;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        UrlRewriteFinder $urlRewriteFinder,
        ConfigProvider $configProvider,
        CreateRedirect $createRedirect,
        PathGetter $categoryPathGetter,
        ProductRepository $productRepository = null, // TODO move to not optional
        ProductUrlPathGenerator $productPathGenerator = null, // TODO move to not optional
        CategoryRepository $categoryRepository = null // TODO move to not optional
    ) {
        $this->urlRewriteFinder = $urlRewriteFinder;
        $this->configProvider = $configProvider;
        $this->createRedirect = $createRedirect;
        $this->categoryPathGetter = $categoryPathGetter;
        // OM for backward compatibility
        $this->productRepository = $productRepository ?? ObjectManager::getInstance()->get(ProductRepository::class);
        $this->productPathGenerator = $productPathGenerator ??
            ObjectManager::getInstance()->get(ProductUrlPathGenerator::class);
        $this->categoryRepository = $categoryRepository ??
            ObjectManager::getInstance()->get(CategoryRepository::class);
    }

    /**
     * @param int $entityId
     * @param int $storeId
     * @return void
     */
    public function execute(int $entityId, int $storeId): void
    {
        if ($this->configProvider->isRedirectsForDeletedProductsEnabled($storeId)) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productRepository->getById($entityId, false, $storeId);
            $categoryIdsRedirect = $product->getCategoryIds();
            $lifetime = $this->configProvider->getRedirectLifetimeForProducts($storeId);
            $redirectType = $this->configProvider->getRedirectTypeForProducts($storeId);

            $categoryIdsRedirect = $this->createRedirectByRewrites(
                $entityId,
                $storeId,
                $categoryIdsRedirect,
                $redirectType,
                $lifetime
            );

            if (!empty($categoryIdsRedirect)) {
                foreach ($categoryIdsRedirect as $categoryId) {
                    $categoryId = (int)$categoryId;
                    $category = $this->categoryRepository->get($categoryId, $storeId);
                    $product->setCategoryId($categoryId);

                    $this->createRedirect->withParams(
                        $storeId,
                        $this->productPathGenerator->getUrlPathWithSuffix($product, $storeId, $category),
                        $this->categoryPathGetter->getByCategory($category),
                        $redirectType,
                        $lifetime
                    );
                }
            }
        }
    }

    private function createRedirectByRewrites(
        int $entityId,
        int $storeId,
        array $categoryIds,
        string $redirectType,
        string $lifetime
    ): array {
        $currentUrlRewrites = $this->urlRewriteFinder->findAllByData(
            $entityId,
            $storeId,
            ProductUrlRewriteGenerator::ENTITY_TYPE
        );
        foreach ($currentUrlRewrites as $urlRewrite) {
            $categoryPath = '/';

            $categoryId = $this->resolveCategoryId($urlRewrite);

            if ($categoryId) {
                $this->markCategoryId($categoryId, $categoryIds);
                $categoryPath = $this->categoryPathGetter->execute(
                    $categoryId,
                    $storeId
                );
            }

            $this->createRedirect->execute($urlRewrite, $categoryPath, $redirectType, $lifetime);
        }

        return $categoryIds;
    }

    private function resolveCategoryId(\Magento\UrlRewrite\Service\V1\Data\UrlRewrite $urlRewrite): ?int
    {
        if (($metaData = $urlRewrite->getMetadata()) && isset($metaData['category_id'])) {
            return (int)$metaData['category_id'];
        }

        return null;
    }

    private function markCategoryId(int $categoryId, array &$categoryIds): void
    {
        $key = array_search($categoryId, $categoryIds, false);
        if ($key !== false) {
            unset($categoryIds[$key]);
        }
    }
}
