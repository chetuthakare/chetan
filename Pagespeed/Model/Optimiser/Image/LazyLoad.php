<?php
namespace Swissup\Pagespeed\Model\Optimiser\Image;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Swissup\Pagespeed\Helper\Config;

class LazyLoad extends AbstractImage
{
    /**
     *
     * @var \Swissup\Pagespeed\Model\Optimiser\Preloader
     */
    private $preloader;

    /**
     * @param Config $config
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Swissup\Pagespeed\Model\Optimiser\Preloader $preloader
     */
    public function __construct(
        Config $config,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Swissup\Pagespeed\Model\Optimiser\Preloader $preloader
    ) {
        parent::__construct($config, $cache, $cacheState, $serializer);
        $this->preloader = $preloader;
    }

    /**
     *
     * @param string $src
     * @return boolean
     */
    protected function isIgnored($src)
    {
        return $this->config->isImageLazyLoadIgnored($src);
    }

    /**
     *
     * @return int
     */
    protected function getOffset()
    {
        $offset = $this->config->isMobile() ?
            $this->config->getLazyloadMobileOffset() : $this->config->getLazyloadOffset();
        $offset = $offset + 1;

        return $offset;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isImageLazyLoadEnable() || $response === null) {
            return $response;
        }
        $html = $response->getBody();
        $images = $this->getImagesFromHtml($html);

        if (empty($images)) {
            return $response;
        }

        $offset = $this->getOffset();

        $preloadImages = [];
        foreach ($images as $image) {
            /** @var \DOMElement $node */
            $node = $this->getDOMNodeFromImageHtml($image);
            $src = $node->getAttribute('src');

            if ($node->getAttribute('loading') || $this->isIgnored($src)) {
                continue;
            }

            // preload first X number of images
            if (--$offset > 0) {
                $isParentPicture = $this->isParentTagPicture($html, $image);
                if (!empty($src) && !$isParentPicture) {
                    $preloadImages[] = $src;
                }
                continue;
            }

            // lazyload all except first X number of images
            $node->setAttribute('loading', 'lazy');
            $lazyloadImage = $this->getImageHtmlFromDOMNode($node);
            $html = str_replace($image, $lazyloadImage, $html);
        }

        $response->setBody($html);

        $this->preloader->push($response, $preloadImages, 'image', 'preload');

        return $response;
    }
}
