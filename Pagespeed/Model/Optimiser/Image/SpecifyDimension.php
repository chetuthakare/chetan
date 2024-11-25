<?php
namespace Swissup\Pagespeed\Model\Optimiser\Image;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Swissup\Pagespeed\Model\Optimiser\Image\WebP as WebPOptimiser;

class SpecifyDimension extends WebPOptimiser
{
    /**
     * @var \Swissup\Image\Helper\Dimensions
     */
    private $imageSize;

    /**
     * @param Config $config
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Swissup\Pagespeed\Model\Image\FileFactory $ioFileFactory
     * @param \Swissup\Image\Helper\Dimensions $imageSize
     */
    public function __construct(
        Config $config,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Swissup\Pagespeed\Model\Image\FileFactory $ioFileFactory,
        \Swissup\Image\Helper\Dimensions $imageSize
    ) {
        parent::__construct($config, $cache, $cacheState, $serializer, $ioFileFactory);

        $this->imageSize = $imageSize;
    }

    /**
     * @param  string $src
     * @return mixed|boolean|array
     */
    protected function getDimensions($src)
    {
        $dimensions = $this->loadCache($src);
        if ($dimensions === false) {
            $dimensions = $this->imageSize->getDimensions($src);
            $this->saveCache($src, $dimensions);
        }

        return $dimensions;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isDimensionEnable() || $response === null) {
            return $response;
        }
        $html = $response->getBody();

        $images = $this->getImagesFromHtml($html);

        foreach ($images as $image) {
            $imageHTML = $image;
            /** @var \DOMElement $node */
            $node = $this->getDOMNodeFromImageHtml($imageHTML);

            $_attrValueFlag = false;
            foreach (['height', 'width'] as $_attributeName) {
                $_attrValue = $node->getAttribute($_attributeName);
                if (!empty($_attrValue)) {
                    $_attrValueFlag = true;
                    break;
                }
            }
            if ($_attrValueFlag) {
                continue;
            }

            foreach (['src', 'data-src'] as $attrName) {
                $attrValue = $node->getAttribute($attrName);
                if (empty($attrValue)) {
                    continue;
                }

                // Optimization for luma-based themes
                if ($node->hasAttribute('max-width') && $node->hasAttribute('max-height')) {
                    $dimensions = [
                        'width' => $node->getAttribute('max-width'),
                        'height' => $node->getAttribute('max-height'),
                    ];
                } else {
                    $dimensions = $this->getDimensions($attrValue);
                }

                if (empty($dimensions['width'])) {
                    continue;
                }

                $node->setAttribute('width', $dimensions['width']);
                $node->setAttribute('height', $dimensions['height']);
                $_image = $this->getImageHtmlFromDOMNode($node);

                if (empty($_image) || strpos($_image, '="\"') !== false) {
                    continue;
                }
                $html = str_replace($image, $_image, $html);
            }
        }

        $response->setBody($html);

        return $response;
    }
}
