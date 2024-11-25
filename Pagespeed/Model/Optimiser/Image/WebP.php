<?php
namespace Swissup\Pagespeed\Model\Optimiser\Image;

use Magento\Framework\App\Response\Http as ResponseHttp;
use Swissup\Pagespeed\Helper\Config;

class WebP extends AbstractImage
{
    /**
     * Cache state name
     */
    const CACHE_STATE = 'SW_PS_WEBP_IS_FILE_EXIST';

    /**
     * @var \Swissup\Pagespeed\Model\Image\FileFactory
     */
    protected $ioFileFactory;

    /**
     * @var \Swissup\Pagespeed\Model\Image\File
     */
    protected $ioFile;

    /**
     * @param \Swissup\Pagespeed\Helper\Config $config
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Swissup\Pagespeed\Model\Image\FileFactory $ioFileFactory
     */
    public function __construct(
        \Swissup\Pagespeed\Helper\Config $config,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Swissup\Pagespeed\Model\Image\FileFactory $ioFileFactory
    ) {
        parent::__construct($config, $cache, $cacheState, $serializer);
        $this->ioFileFactory = $ioFileFactory;
    }

    /**
     * @return \Swissup\Pagespeed\Model\Image\File
     */
    protected function getIoFile()
    {
        if ($this->ioFile === null) {
            $this->ioFile = $this->ioFileFactory->create();
        }
        return $this->ioFile;
    }

    /**
     *
     * @param  string  $imageUrl
     * @return boolean
     */
    protected function isMediaImageFileExist($imageUrl, $useCache = true)
    {
        // $return = $useCache ? $this->loadCache($imageUrl) : false;
        $return = $useCache ? $this->loadCacheLayerValue(self::CACHE_STATE, $imageUrl) : false;
        if ($return === false) {
            $return = $this->getIoFile()->isMediaImageFileExist($imageUrl);
            if ($useCache) {
                // $this->saveCache($imageUrl, $return ? 1 : 0);
                $this->setCacheLayerValue(self::CACHE_STATE, $imageUrl, $return ? 1 : 0);
            }
        }

        return (bool) $return;
    }

    /**
     *
     * @param  string  $imageUrl
     * @return boolean
     */
    protected function isPubStaticImageFileExist($imageUrl, $useCache = true)
    {
        // $return = $useCache ? $this->loadCache($imageUrl) : false;
        $return = $useCache ? $this->loadCacheLayerValue(self::CACHE_STATE, $imageUrl) : false;
        if ($return === false) {
            $return = $this->getIoFile()->isPubStaticImageFileExist($imageUrl);
            if ($useCache) {
                $this->setCacheLayerValue(self::CACHE_STATE, $imageUrl, $return ? 1 : 0);
            }
        }

        return (bool) $return;
    }

    /**
     *
     * @return array
     */
    private function getSrcAttributeNames()
    {
        return [
            'src',
            'srcset',
        ];
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isWebPSupport() || $response === null) {
            return $response;
        }
        \Magento\Framework\Profiler::start(__METHOD__);
        $html = $response->getBody();

        $images = $this->getImagesFromHtml($html);

        $isAddPictureTag = $this->config->isWebPAddPictureTag();
        $_imageHTML = '';
        $srcAttributes = $this->getSrcAttributeNames();
        $alreadyReplacedImages = [];

        foreach ($images as $imageHTML) {
            if (isset($alreadyReplacedImages[$imageHTML])) {
                continue;
            }
            $_imageHTML = $imageHTML;
            $_imageHTML = preg_replace('/\\\\/', '', $_imageHTML);
            $hasSlashes = $_imageHTML !== $imageHTML;

            /** @var \DOMElement $node */
            $node = $this->getDOMNodeFromImageHtml($_imageHTML);
            $originalNode = clone $node;

            $hasWebPImageUrl = false;
            $webPImageUrl = $imageUrl = false;
            foreach ($srcAttributes as $attrName) {
                $attrValue = $node->getAttribute($attrName);
                if (empty($attrValue)) {
                    continue;
                }
                $imageUrls = explode(',', $attrValue);
                $webPImageUrl = false;

                foreach ($imageUrls as $imageUrlPart) {
                    $imageUrlPart = trim($imageUrlPart);
                    list($imageUrl, ) = explode(' ', $imageUrlPart, 2);

                    $webPImageUrl = $this->getWebPImageUrl($imageUrl);
                    if (empty($webPImageUrl)) {
                        continue;
                    }

                    // $headers = @get_headers($webPImageUrl);
                    // if ($headers === false || strpos($headers[0], '404') !== false) {
                    //     continue;
                    // }
                    $hasWebPImageUrl = true;
                    $attrValue = str_replace($imageUrl, $webPImageUrl, $attrValue);
                }

                $node->setAttribute($attrName, $attrValue);
            }
            $newImageHTML = $this->getImageHtmlFromDOMNode($node);

            if ($isAddPictureTag &&
                $hasWebPImageUrl &&
                $webPImageUrl &&
                $imageHTML !== $newImageHTML
            ) {
                $isImgAlreadyInsidePicture = $this->isParentTagPicture($html, $imageHTML)/* || ($node->parentNode && $node->parentNode->nodeName === 'picture')*/;

                $ioFile = $this->getIoFile();
                $filename = $ioFile->getFileBasename($imageUrl);
                $extension = $ioFile->getFileExtension($filename);
                $newImageHTML = $this->buildPictureHtml($originalNode);

                // Add original IMG into picture as fallback for really old browsers
                $newImageHTML = str_replace(
                    '</picture>',
                    $imageHTML . '</picture>',
                    $newImageHTML
                );

                // When IMG is already in PICTURE remove wrapper PICTURE from generated HTML
                if ($isImgAlreadyInsidePicture) {
                    $newImageHTML = str_replace(['<picture>', '</picture>'], '', $newImageHTML);
                }
            }
            if ($hasSlashes) {
                $newImageHTML = addslashes($newImageHTML);
                $newImageHTML = str_replace('/', '\/', $newImageHTML);
            }
            if (empty($newImageHTML) || strpos($newImageHTML, '="\"') !== false) {
                continue;
            }
            $alreadyReplacedImages[$imageHTML] = true;
            $html = str_replace($imageHTML, $newImageHTML, $html);
        }

        if ($this->config->isReplaceWebPInJs()) {
            $html = $this->replaceInMageInitContent($html);
            $html = $this->replaceInBackgroungImagesAtributes($html);
        }

        $this->saveCacheLayer(self::CACHE_STATE);
        $response->setBody($html);
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $response;
    }

    /**
     * @param string $html
     * @return string
     */
    private function replaceInMageInitContent($html)
    {
        $regExpForXMagentoInit = '/<script.*type=\"(text\/x-magento-init).*>(.*)<\/script>/sU';
        $regExpForImageUrl = "/(?:https?:\\\\\/\\\\\/[^\/\s]+\/\S+\.(?:jpe?g|png))/sU";
        $scriptMatches = $imageUrlMatches2 = $jsReplacements = [];
        preg_match_all($regExpForXMagentoInit, (string) $html, $scriptMatches);
        foreach ($scriptMatches[0] as $xMagentoInitContent) {
            preg_match_all($regExpForImageUrl, (string) $xMagentoInitContent, $imageUrlMatches2);
            foreach ($imageUrlMatches2[0] as $imageUrl) {
                $encodedImageUrl = str_replace('\/', '/', $imageUrl);

                $webPImageUrl = $this->getWebPImageUrl($encodedImageUrl);
                if (empty($webPImageUrl)) {
                    continue;
                }

                $webPImageUrl = str_replace('/', '\/', $webPImageUrl);
                $jsReplacements[$imageUrl] = $webPImageUrl;
            }
        }
        $html = str_replace(array_keys($jsReplacements), $jsReplacements, $html);
        // $html = strtr($html, $jsReplacements);

        return $html;
    }

    /**
     * @param string $html
     * @return string
     */
    private function replaceInBackgroungImagesAtributes($html)
    {
        $attributeMatches = $imageUrlMatches = $jsReplacements = [];
        $backgroundImagesPattern = '/data-background-images=(?:\'|"){.+}(?:\'|")/';
        $regExpForImageUrl = '/(?:https?:\/\/[^\/\s]+\/\S+\.(?:jpe?g|png))/sU';
        preg_match_all($backgroundImagesPattern, (string) $html, $attributeMatches);
        foreach ($attributeMatches[0] as $backgroundImageAttribute) {
            preg_match_all($regExpForImageUrl, (string) $backgroundImageAttribute, $imageUrlMatches);
            foreach ($imageUrlMatches[0] as $imageUrl) {
                $webPImageUrl = $this->getWebPImageUrl($imageUrl);
                if (empty($webPImageUrl)) {
                    continue;
                }
                $jsReplacements[$imageUrl] = $webPImageUrl;
            }
        }
        $html = str_replace(array_keys($jsReplacements), $jsReplacements, $html);

        return $html;
    }

    /**
     * @param string $filename
     * @return mixed
     */
    protected function getFileExtension($filename)
    {
        $pathInfo = $this->getIoFile()->getPathInfo($filename);
        $part = 'extension';
        return isset($pathInfo[$part]) ? $pathInfo[$part] : false;
    }

    /**
     *
     * @param  string $imageUrl
     * @return string|boolean
     */
    private function getWebPImageUrl($imageUrl)
    {
        $allowedExtensions = explode(',', 'jpeg,jpg,png');

        $ioFile = $this->getIoFile();
        $basename = $ioFile->getFileBasename($imageUrl);
        $extension = $ioFile->getFileExtension($basename);

        if (!in_array(strtolower($extension), $allowedExtensions)) {
            return false;
        }
        $filename = $ioFile->getFilename($basename);
        $filenames = [
            $filename . '.' . $extension . '.webp',
            $filename . '.webp'
        ];

        foreach ($filenames as $newFilename) {
            $webPUrl = str_replace('/' . $basename, '/' . $newFilename, $imageUrl);

            if (($ioFile->isMediaProductUrl($webPUrl) || $ioFile->isMediaCustomUrl($webPUrl))
                && $this->isMediaImageFileExist($webPUrl)
            ) {
                return $webPUrl;
            }

            if ($ioFile->isPubStaticUrl($webPUrl) && $this->isPubStaticImageFileExist($webPUrl)) {
                return $webPUrl;
            }
        }

        return false;
    }

    /**
     * Build PICTURE tag with two sorces - WebP and orignal
     *
     * @param  \DOMElement $node
     * @return string
     */
    private function buildPictureHtml($node)
    {
        $imageUrls = [];
        foreach ($this->getSrcAttributeNames() as $attrName) {
            $attrValue = $node->getAttribute($attrName);
            $items = explode(',', $attrValue);
            $urls = array_map(function ($item) {
                $item = trim($item);
                list($url, ) = explode(' ', $item, 2);

                return $url;
            }, $items);

            array_push($imageUrls, ...$urls);
        }

        $imageUrls = array_filter($imageUrls);
        $imageUrls = array_unique($imageUrls);
        $webPUrls = array_map([$this, 'getWebPImageUrl'], $imageUrls);
        $pictureHtml = "<picture>\n";

        $attributes = $this->getSrcAttributeNames();
        $attributes[] = 'sizes';
        $source = [];
        $this->sourceAttrSet($source, 'type', 'image/webp');
        foreach ($attributes as $attrName) {
            if ($attrValue = $node->getAttribute($attrName)) {
                $attrValue = str_replace($imageUrls, $webPUrls, $attrValue);
                $this->sourceAttrSet($source, $attrName, $attrValue);
            }
        }

        $pictureHtml .= $this->sourceRender($source);
        $ioFile = $this->getIoFile();
        $basename = $ioFile->getFileBasename(reset($imageUrls));
        $extension = $ioFile->getFileExtension($basename);
        $source = [];
        $this->sourceAttrSet($source, 'type', "image/{$extension}");
        foreach ($attributes as $attrName) {
            if ($attrValue = $node->getAttribute($attrName)) {
                $this->sourceAttrSet($source, $attrName, $attrValue);
            }
        }

        $pictureHtml .= $this->sourceRender($source);
        $pictureHtml .= '</picture>';

        return $pictureHtml;
    }

    /**
     * Set attribute for `source` tag
     *
     * @param  array  &$source Tag `source` presented as array
     * @param  string $attr
     * @param  string $value
     */
    private function sourceAttrSet(array &$source, $attr, $value)
    {
        $map = [
            'src' => 'srcset',
            'data-src' => 'data-srcset'
        ];

         $source[$map[$attr] ?? $attr] = $value;
    }

    /**
     * Render html for `source` tag
     *
     * @param  array  $source Tag `source` presented as array
     * @return string
     */
    private function sourceRender(array $source)
    {
        array_walk($source, function (&$value, $attr) {
            $value = "{$attr}=\"{$value}\"";
        });

        return '<source ' . implode(' ', $source) . " />\n";
    }
}
