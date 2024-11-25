<?php
declare(strict_types=1);

namespace Swissup\Pagespeed\Service;

use Magento\Framework\Exception\NotFoundException;
use Swissup\Pagespeed\Model\Image\Product\ImageGenerator as ProductImageGenarator;
use Swissup\Pagespeed\Model\Image\Custom\ImageGenerator as CustomImageGenarator;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImageResize
{
    /**
     * @var ProductImageGenarator
     */
    private $productImageGenerator;

    /**
     * @var CustomImageGenarator
     */
    private $customImageGenarator;

    /**
     * @var \Swissup\Pagespeed\Model\Image\Resizer
     */
    private $imageResizer;

    /**
     * @var \Swissup\Pagespeed\Model\Image\Scaler
     */
    private $imageScaler;

    /**
     * @var \Swissup\Pagespeed\Model\Image\ParamsBuilder
     */
    private $paramsBuilder;

    /**
     * @var \Swissup\Pagespeed\Model\View\GetCatalogMediaEntities
     */
    private $getCatalogMediaEntities;

    /**
     * @var \Swissup\Pagespeed\Model\Image\Product\DestanationPathResolver
     */
    private $destinationPathResolver;

    /**
     * @var \Swissup\Image\Helper\Dimensions
     */
    private $imageDimension;

    /**
     *
     * @var string
     */
    private $filenameFilter;

    /**
     *
     * @var integer
     */
    private $limit = 100000;

    /**
     * @param ProductImageGenarator $productImageGenerator
     * @param CustomImageGenarator $customImageGenarator
     * @param \Swissup\Pagespeed\Model\Image\Resizer $imageResizer
     * @param \Swissup\Pagespeed\Model\Image\Scaler $imageScaler
     * @param \Swissup\Pagespeed\Model\Image\ParamsBuilder $paramsBuilder
     * @param \Swissup\Pagespeed\Model\View\GetCatalogMediaEntities $getCatalogMediaEntities
     * @param \Swissup\Pagespeed\Model\Image\Product\DestanationPathResolver $destinationPathResolver
     * @param \Swissup\Image\Helper\Dimensions $imageDimension
     * @internal param ProductImage $gallery
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ProductImageGenarator $productImageGenerator,
        CustomImageGenarator $customImageGenarator,
        \Swissup\Pagespeed\Model\Image\Resizer $imageResizer,
        \Swissup\Pagespeed\Model\Image\Scaler $imageScaler,
        \Swissup\Pagespeed\Model\Image\ParamsBuilder $paramsBuilder,
        \Swissup\Pagespeed\Model\View\GetCatalogMediaEntities $getCatalogMediaEntities,
        \Swissup\Pagespeed\Model\Image\Product\DestanationPathResolver $destinationPathResolver,
        \Swissup\Image\Helper\Dimensions $imageDimension
    ) {
        $this->productImageGenerator = $productImageGenerator;
        $this->customImageGenarator = $customImageGenarator;
        $this->imageResizer = $imageResizer;
        $this->imageScaler = $imageScaler;
        $this->paramsBuilder = $paramsBuilder;
        $this->getCatalogMediaEntities = $getCatalogMediaEntities;
        $this->destinationPathResolver = $destinationPathResolver;
        $this->imageDimension = $imageDimension;
    }

    /**
     * Create resized images of different sizes from themes
     * @param array|null $themes
     * @return \Generator
     * @throws NotFoundException
     */
    public function resizeAllProductImages(array $themes = null): \Generator
    {
        if (!empty($this->filenameFilter)) {
            $this->productImageGenerator->setFilenameFilter($this->filenameFilter);
        }

        $count = $this->productImageGenerator->getCountAllProductImages();
        if (!$count) {
            yield 'None' => 0;
            // throw new NotFoundException(__('Cannot resize images - product images not found'));
        }

//        $images = [];
        $productImageGenerator = $this->productImageGenerator
            ->setPageSize($this->limit)
            ->create();
        foreach ($productImageGenerator as $image) {
            $images[] = $image;
        }
        $count = count($images);
        if (!$count) {
            yield 'None' => 0;
            // throw new NotFoundException(__('Cannot resize images - product images not found'));
        }

        $mediaEntities = $this->getCatalogMediaEntities->get($themes ?? []);
        foreach ($images as $image) {
            $originalImageName = $image['filename'];
            $originalImagePath = $image['path'];

            foreach ($mediaEntities as $mediaEntity) {
                $imageParams = $this->paramsBuilder->build($mediaEntity);
                $destinationImagePath = $this->destinationPathResolver
                    ->setName($originalImageName)
                    ->setParams($imageParams)
                    ->getPath();

                $this->imageResizer
                    ->setOrigin($originalImagePath)
                    ->setParams($imageParams)
                    ->setDestination($destinationImagePath)
                    ->execute();

                if (empty($imageParams['image_width']) || empty($imageParams['image_height'])) {
                    $dimensions = $this->imageDimension->getDimensions($destinationImagePath);
                    if (!empty($dimensions['width']) && !empty($dimensions['height'])) {
                        $imageParams['image_width'] = $dimensions['width'];
                        $imageParams['image_height'] = $dimensions['height'];
                    }
                }

                if (!empty($imageParams['image_width']) && !empty($imageParams['image_height'])) {
                    $resolutions = [0.5, 0.75,/*1,*/ 2, 3];
                    $this->imageScaler->setOrigin($originalImagePath)
                        ->setParams($imageParams)
                        ->setDestination($destinationImagePath)
                        ->setResolutions($resolutions)
                        ->execute()
                    ;
                }
            }

            yield $originalImageName => $count;
        }
    }

    /**
     *
     * @return \Generator
     * @throws NotFoundException
     */
    public function resizeCustomImages()
    {
        $mediaEntity = [
            'type' => "image",
            'id' => "swissup_pagespeed_wysiwyg_default"
        ];
        $imageParams = $this->paramsBuilder->build($mediaEntity);

        $customImageGenerator = $this->customImageGenarator
            ->setFilenameFilter($this->filenameFilter)
            ->setPageSize($this->limit)
            ->create();

        $images = [];
        foreach ($customImageGenerator as $image) {
            $images[] = $image;
        }
        $count = count($images);
        if (!$count) {
            yield 'None' => 0;
        }

        foreach ($images as $image) {
            $canResize = true;
            $originalImageName = $image['filename'];
            $originalImagePath = $image['path'];
            $dimensions = $this->imageDimension->getDimensions($originalImagePath);
            if (empty($dimensions['width'])) {
                $canResize = false;
            } else {
                $imageParams['image_width'] = $dimensions['width'];
                $imageParams['image_height'] = $dimensions['height'];
            }

            if ($canResize) {
                $destinationImagePath = $originalImagePath;

                $this->imageResizer
                    ->setOrigin($originalImagePath)
                    ->setParams($imageParams)
                    ->setDestination($destinationImagePath)
                    ->execute();

                $resolutions = [0.5, 0.75];
                $this->imageScaler
                    ->setOrigin($originalImagePath)
                    ->setParams($imageParams)
                    ->setDestination($destinationImagePath)
                    ->setResolutions($resolutions)
                    ->execute()
                ;
            }
            yield $originalImageName => $count;
        }
    }

    /**
     *
     * @param string $filename
     */
    public function setFilenameFilter($filename)
    {
        $this->filenameFilter = (string) $filename;
        return $this;
    }

    /**
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }
}
