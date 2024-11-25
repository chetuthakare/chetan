<?php
namespace Swissup\Pagespeed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State as AppState;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Mobile_Detect as MobileDetect;

class Config extends AbstractHelper
{
    const CONFIG_XML_PATH_ENABLE             = 'pagespeed/main/enable';
    const CONFIG_XML_PATH_DEV_MODE           = 'pagespeed/main/devmode';
    const CONFIG_XML_PATH_HTTP2_PUSH         = 'pagespeed/main/server_push';
    const CONFIG_XML_PATH_LINK_PRELOAD_ENABLED = 'pagespeed/main/link_preload';
    const CONFIG_XML_PATH_CUSTOM_PRELOAD_LINK  = 'pagespeed/main/custom_preload_link';
    const CONFIG_XML_PATH_CONTENT_ENABLE     = 'pagespeed/content/enable';
    const CONFIG_XML_PATH_CONTENT_JS         = 'pagespeed/content/js';
    const CONFIG_XML_PATH_CONTENT_CSS        = 'pagespeed/content/css';
    const CONFIG_XML_PATH_JS_MERGE           = 'dev/js/merge_files';
    const CONFIG_XML_PATH_JS_ENABLE_JS_BUNDLING = 'dev/js/enable_js_bundling';
    const CONFIG_XML_PATH_JS_ENABLE_ADVANCED_JS_BUNDLING = 'pagespeed/js/enable_advanced_js_bundling';
    const CONFIG_XML_PATH_JS_RJS_BUILD_CONFIG = 'pagespeed/js/rjs_build_config';
    const CONFIG_XML_PATH_JS_DEFER           = 'pagespeed/js/defer_enable';
    // const CONFIG_XML_PATH_JS_MOVE_INLINE_TO_BOTTOM = 'dev/js/move_inline_to_bottom';
    const CONFIG_XML_PATH_JS_DEFER_UNPACK    = 'pagespeed/js/defer_unpack';
    const CONFIG_XML_PATH_JS_DEFER_IGNORE    = 'pagespeed/js/defer_ignore';
    const CONFIG_XML_PATH_USE_CSS_CRITICAL_PATH = 'dev/css/use_css_critical_path';
    const CONFIG_XML_PATH_CSS_CRITICAL_ENABLE = 'pagespeed/css/critical_enable';
    const CONFIG_XML_PATH_CSS_CRITICAL_DEFAULT = 'pagespeed/css/critical_default';
    const CONFIG_XML_PATH_CSS_CRITICAL_LAYOUT = 'pagespeed/css/critical_layout';
    const CONFIG_XML_PATH_EXPIRE_ENABLE      = 'pagespeed/expire/enable';
    const CONFIG_XML_PATH_EXPIRE_TTL         = 'pagespeed/expire/ttl';
    const CONFIG_XML_PATH_DNSPREFETCH_ENABLE = 'pagespeed/dnsprefetch/enable';
    const CONFIG_XML_PATH_PRECONNECT_ENABLE = 'pagespeed/preconnect/enable';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_ENABLE = 'pagespeed/image/optimize_enable';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_PROVIDER= 'pagespeed/image/provider';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_PROVIDER_APIURL = 'pagespeed/image/provider_apiurl';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_PROVIDER_APIKEY = 'pagespeed/image/provider_apikey';

    const CONFIG_XML_PATH_IMAGE_OPTIMISE_CRON_ENABLE = 'pagespeed/image/optimize_cron_enable';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_CRON_LIMIT = 'pagespeed/image/optimize_cron_limit';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_WEBP_ENABLE = 'pagespeed/image/optimize_webp_enable';
    const CONFIG_XML_PATH_IMAGE_OPTIMIZE_TIMEOUT = 'pagespeed/image/optimize_timeout';
    const CONFIG_XML_PATH_IMAGE_OPTIMIZE_LOGGING = 'pagespeed/image/optimize_logging';
    const CONFIG_XML_PATH_IMAGE_OPTIMISE_WEBP_PICTURE_ADD = 'pagespeed/image/optimize_webp_picture_add';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_ENABLE = 'pagespeed/image/lazyload_enable';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_IGNORE = 'pagespeed/image/lazyload_ignore';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_OFFSET = 'pagespeed/image/lazyload_offset';
    const CONFIG_XML_PATH_IMAGE_LAZYLOAD_MOBILE_OFFSET = 'pagespeed/image/lazyload_mobile_offset';
    const CONFIG_XML_PATH_IMAGE_DIMENSION  = 'pagespeed/image/dimension';
    const CONFIG_XML_PATH_IMAGE_RESPONSIVE_ENABLE = 'pagespeed/image/responsive';
    const CONFIG_XML_PATH_IMAGE_RESPONSIVE_SIZES = 'pagespeed/image/default_responsive_sizes';

    /**
     * @var string
     */
    private $stateMode;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectoryWriter;

    /**
     *
     * @var MobileDetect
     */
    private $detector;

    /**
     *
     * @var \Swissup\Pagespeed\Model\Config\Backend\File\RjsFactory
     */
    private $rjsConfigFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    private $serializer;

    /**
     *
     * @var array
     */
    private $lazyloadIgnores;

    /**
     * @param Context $context
     * @param AppState $state
     * @param Filesystem $filesystem
     * @param MobileDetect $detector
     * @param \Swissup\Pagespeed\Model\Config\Backend\File\RjsFactory $rjsConfigFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        Context $context,
        AppState $state,
        Filesystem $filesystem,
        MobileDetect $detector,
        \Swissup\Pagespeed\Model\Config\Backend\File\RjsFactory $rjsConfigFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        parent::__construct($context);
        $this->stateMode = $state->getMode();
        $this->mediaDirectoryWriter = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->detector = $detector;
        $this->rjsConfigFactory = $rjsConfigFactory;
        $this->serializer = $serializer;
    }

    /**
     *
     * @param  string $key
     * @return mixed
     */
    private function getConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @param  string $key
     * @return boolean
     */
    private function isSetFlag($key)
    {
        return $this->scopeConfig->isSetFlag($key, ScopeInterface::SCOPE_STORE);
    }

    /**
     *
     * @return boolean
     */
    public function isDeveloperMode()
    {
        return $this->stateMode === AppState::MODE_DEVELOPER;
    }

    /**
     *
     * @return boolean
     */
    public function isDeveloperModeIgnored()
    {
        return $this->isSetFlag(self::CONFIG_XML_PATH_DEV_MODE);
    }

    /**
     *
     * @return boolean
     */
    public function isEnable()
    {
        return (!$this->isDeveloperMode() || $this->isDeveloperModeIgnored()) && $this->isSetFlag(self::CONFIG_XML_PATH_ENABLE);
    }

    /**
     *
     * @return boolean
     */
    public function isContentMinifyEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CONTENT_ENABLE);
    }

    /**
     *
     * @return boolean
     */
    public function isContentJsMinifyEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CONTENT_JS);
    }

    /**
     *
     * @return boolean
     */
    public function isContentCssMinifyEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_CONTENT_CSS);
    }

    /**
     *
     * @return boolean
     */
    public function isAddExpireEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_EXPIRE_ENABLE);
    }

    /**
     *
     * @return int
     */
    public function getExpireTTL()
    {
        return (int) $this->getConfig(self::CONFIG_XML_PATH_EXPIRE_TTL);
    }

    /**
     *
     * @return boolean
     */
    public function isDnsPrefetchEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_DNSPREFETCH_ENABLE);
    }

    /**
     *
     * @return boolean
     */
    public function isPreconnectEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_PRECONNECT_ENABLE);
    }

    /**
     *
     * @return boolean
     */
    public function isJsMergeEnable()
    {
        return $this->isSetFlag(self::CONFIG_XML_PATH_JS_MERGE);
    }

    /**
     *
     * @return boolean
     */
    public function isDeferJsEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_JS_DEFER);
    }

    /**
     *
     * @return boolean
     */
    public function isDeferJsUnpackEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_JS_DEFER_UNPACK);
    }

    /**
     *
     * @return array
     */
    public function getDeferJsIgnores()
    {
        $ignores = explode("\n", (string) $this->getConfig(self::CONFIG_XML_PATH_JS_DEFER_IGNORE));
        foreach ($ignores as &$ignore) {
            $ignore = trim($ignore, " \r");
        }
        $ignores = array_filter($ignores);
        $ignores = array_unique($ignores);
        return $ignores;
    }

    /**
     *
     * @return boolean
     */
    public function isMergeJsFilesForMobileDisabled()
    {
        return false;
//        return false && $this->detector->isMobile();
    }

    /**
     *
     * @return boolean
     */
    public function isMobile()
    {
        return $this->detector->isMobile();
    }

    /**
     *
     * @return boolean
     */
    public function isMergeCssFilesForMobileDisabled()
    {
        return false;
//        return false && $this->isMobile();
    }

    /**
     *
     * @return boolean
     */
    public function isAutoAddFontDisplayForMergedCss()
    {
        return $this->isEnable();
        // return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_);
    }

    /**
     *
     * @return boolean
     */
    public function isImageLazyLoadEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_ENABLE);
    }

    /**
     *
     * @return array
     */
    public function getLazyloadIgnores()
    {
        $ignores = explode("\n", (string) $this->getConfig(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_IGNORE));
        foreach ($ignores as &$ignore) {
            $ignore = trim($ignore, " \r");
        }
        $ignores = array_filter($ignores);
        return $ignores;
    }

    /**
     *
     * @param string $src
     * @return boolean
     */
    public function isImageLazyLoadIgnored($src)
    {
        if ($this->lazyloadIgnores === null) {
            $this->lazyloadIgnores = $this->getLazyloadIgnores();
        }
        foreach ($this->lazyloadIgnores as $ignore) {
            if (false !== strstr($src, $ignore)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @return int
     */
    public function getLazyloadOffset()
    {
        return  (int) $this->getConfig(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_OFFSET);
    }

    /**
     *
     * @return int
     */
    public function getLazyloadMobileOffset()
    {
        return  (int) $this->getConfig(self::CONFIG_XML_PATH_IMAGE_LAZYLOAD_MOBILE_OFFSET);
    }

    /**
     *
     * @return boolean
     */
    public function isUseCssCriticalPathEnable()
    {
        return $this->isSetFlag(self::CONFIG_XML_PATH_USE_CSS_CRITICAL_PATH);
        //&& $this->isAllowedCriticalCssOnCurrentPage() // call Swissup\Pagespeed\Plugin\Framework\App\ConfigPlugin
    }

    /**
     *
     * @return boolean
     */
    public function isCriticalCssEnable()
    {
        return $this->isEnable()
            && $this->isSetFlag(self::CONFIG_XML_PATH_CSS_CRITICAL_ENABLE)
            && $this->isAllowedCriticalCssOnCurrentPage();
    }

    /**
     *
     * @return bool
     */
    public function isAllowedCriticalCssOnCurrentPage()
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->_getRequest();
        $current = $request->getFullActionName();
        $allowedHandles = [
            'cms_index_index',
            'cms_page_view',
            'catalog_category_view',
            'catalog_product_view'
        ];

        return in_array($current, $allowedHandles);
    }

    /**
     *
     * @return boolean
     */
    public function isCriticalCssThemeHanleMergeEnable()
    {
        return $this->isEnable()
            && $this->isUseCssCriticalPathEnable()
            && $this->isSetFlag(self::CONFIG_XML_PATH_CSS_CRITICAL_LAYOUT);
    }

    /**
     *
     * @return string
     */
    public function getDefaultCriticalCss()
    {
        $value = '';
        $filename = (string) $this->getConfig(self::CONFIG_XML_PATH_CSS_CRITICAL_DEFAULT);
        $writer = $this->mediaDirectoryWriter;

        if ($writer->isExist($filename) &&
            $writer->isFile($filename) &&
            $writer->isReadable($filename)
        ) {
            $_value = $writer->readFile($filename);
            if (!empty($_value)) {
                $value = $_value;
            }
        }

        return trim($value);
    }

    /**
     *
     * @return boolean
     */
    public function isDimensionEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_DIMENSION);
    }

    /**
     *
     * @return boolean
     */
    public function isImageOptimizerEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_ENABLE);
    }

    /**
     *
     * @return int
     */
    public function getImageOptimizerProvider()
    {
        return (int) $this->getConfig(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_PROVIDER);
    }

    /**
     *
     * @return boolean
     */
    public function isImageOptimizersRemote()
    {
        return $this->isImageOptimizerEnable() &&
            $this->getImageOptimizerProvider() === \Swissup\Pagespeed\Model\Config\Source\Image\Optimize\Provider::REMOTE;
    }

    /**
     * Retrieve Base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urlBuilder->getBaseUrl();
    }

    /**
     * Retrieve Media Dir
     *
     * @return string
     */
    public function getMediaDir()
    {
        return $this->mediaDirectoryWriter->getAbsolutePath();
    }

    /**
     * @return string
     */
    public function getImageOptimizeServiceAPIUrl()
    {
        return $this->getConfig(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_PROVIDER_APIURL);
    }

    /**
     * @return string
     */
    public function getImageOptimizeServiceAPIKey()
    {
        return $this->getConfig(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_PROVIDER_APIKEY);
    }

    /**
     *
     * @return boolean
     */
    public function isWebPEnable()
    {
        return $this->isImageOptimizerEnable()
            && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_WEBP_ENABLE);
    }

    /**
     *
     * @return int
     */
    public function getImageOptimizerTimeout()
    {
        return (int) $this->getConfig(self::CONFIG_XML_PATH_IMAGE_OPTIMIZE_TIMEOUT);
    }

    /**
     * @return bool
     */
    public function useLoggingUntilImageOptimise()
    {
        return $this->isImageOptimizerEnable()
            && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_OPTIMIZE_LOGGING)
        ;
    }

    /**
     *
     * @return boolean
     */
    public function isWebPSupport()
    {
        $detector = $this->detector;

        return $this->isWebPEnable()
            && ($detector->is('Chrome')
                || $detector->is('Opera')
                || $detector->isAndroidOS()
                || (int) $detector->version('Firefox') >= 65
                || (int) $detector->version('Chrome') > 32
            );
    }

    /**
     *
     * @return boolean
     */
    public function isWebPAddPictureTag()
    {
        return $this->isWebPEnable()
            && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_WEBP_PICTURE_ADD);
    }

    /**
     *
     * @return boolean
     */
    public function isReplaceWebPInJs()
    {
        return $this->isWebPEnable();
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushEnabled()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_HTTP2_PUSH);
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushForCssEnabled()
    {
        return $this->isHTTP2ServerPushEnabled();
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushForJsEnabled()
    {
        return $this->isHTTP2ServerPushEnabled();
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushForImgEnabled()
    {
        return $this->isHTTP2ServerPushEnabled();
    }

    /**
     *
     * @return boolean
     */
    public function isHTTP2ServerPushForFontEnabled()
    {
        return $this->isHTTP2ServerPushEnabled();
    }

    /**
     *
     * @return boolean
     */
    public function isLinkPreloadEnabled()
    {
        return $this->isEnable()
            && !$this->isHTTP2ServerPushEnabled()
            && $this->isSetFlag(self::CONFIG_XML_PATH_LINK_PRELOAD_ENABLED);
    }

    /**
     *
     * @return array
     */
    public function getCustomLinkForPreload()
    {
        $links = (string) $this->getConfig(self::CONFIG_XML_PATH_CUSTOM_PRELOAD_LINK);
        $links = explode("\n", $links);
        foreach ($links as &$link) {
            $link = trim($link, " \r");
        }
        return $links;
    }

    /**
     *
     * @return boolean
     */
    public function isImageResponsiveEnable()
    {
        return $this->isEnable() && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_RESPONSIVE_ENABLE);
    }

    /**
     *
     * @return string
     */
    public function getDefaultImageResponsiveSizes()
    {
        return (string) $this->getConfig(self::CONFIG_XML_PATH_IMAGE_RESPONSIVE_SIZES);
    }

    /**
     *
     * @return array of string
     */
    public function getResizeCommandTargetDirs()
    {
        return [
            'wysiwyg',
            'catalog/category',
            'easybanner',
            'easyslide',
            'swissup',
            'highlight',
            'easycatalogimg',
            'prolabels',
            'testimonials',
            'mageplaza',
            'lightboxpro',
            'logo',
            'attribute'
        ];
    }

    /**
     *
     * @return boolean
     */
    public function isAdvancedJsBundling()
    {
        return $this->isEnable()
            && !$this->isSetFlag(self::CONFIG_XML_PATH_JS_ENABLE_JS_BUNDLING)
            && $this->isSetFlag(self::CONFIG_XML_PATH_JS_ENABLE_ADVANCED_JS_BUNDLING);
    }

    /**
     *
     * @return array
     */
    public function getRjsJsonConfig()
    {
        $value = (string) $this->getConfig(self::CONFIG_XML_PATH_JS_RJS_BUILD_CONFIG);

        $model = $this->rjsConfigFactory->create()->setValue($value);
        $model->afterLoad();

        $value = $model->getValue();

        json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $value = [];
        } else {
            $value = $this->serializer->unserialize($value);
        }
        return $value;
    }

    /**
     *
     * @return boolean
     */
    public function isCronEnabled()
    {
        return $this->isEnable()
            && $this->isImageOptimizerEnable()
            && $this->isSetFlag(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_CRON_ENABLE);
    }

    /**
     *
     * @return int
     */
    public function getCronLimit()
    {
        return (int) $this->getConfig(self::CONFIG_XML_PATH_IMAGE_OPTIMISE_CRON_LIMIT);
    }
}
