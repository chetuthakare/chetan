<?php

namespace Swissup\Pagespeed\Plugin\Block\Html\Header;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Asset\File\NotFoundException;

class CriticalCssPlugin
{
    /**
     * @var \Swissup\Pagespeed\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Framework\View\Layout
     */
    private $layout;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolver;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @param \Swissup\Pagespeed\Helper\Config $config
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\View\Layout $layout
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Swissup\Pagespeed\Helper\Config $config,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->config = $config;
        /** @var \Magento\Framework\App\Request\Http $request */
        $this->request = $request;
        $this->layout = $layout;
        $this->pageConfig = $pageConfig;
        $this->assetRepo = $assetRepo;
        $this->localeResolver = $localeResolver;
        $this->url = $url;
    }

    /**
     * Will return the currect page layout.
     *
     * @return string The current page layout.
     */
    private function getCurrentPageLayout()
    {
        /** @var string|null $currentPageLayout */
        $currentPageLayout = $this->pageConfig->getPageLayout();

        if ($currentPageLayout === null) {
            /** @var \Magento\Framework\View\Model\Layout\Merge $update */
            $update = $this->layout->getUpdate();
            return $update->getPageLayout();
        }

        return $currentPageLayout;
    }

    /**
     *
     * @return string
     */
    private function getFullActionName()
    {
        return $this->request->getFullActionName();
    }

    /**
     *
     * @param  \Magento\Theme\Block\Html\Header\CriticalCss $subject
     * @param  string $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCriticalCssData(
        \Magento\Theme\Block\Html\Header\CriticalCss $subject,
        $result
    ) {
        if ($this->config->isCriticalCssThemeHanleMergeEnable()) {
            foreach ($this->getAssetGroups() as $group) {
                foreach ($group as $name) {
                    try {
                        $asset = $this->assetRepo->createAsset(
                            'css/critical/' . $name,
                            ['_secure' => 'false']
                        );
                        $result .= $asset->getContent();
                        break;
                    } catch (LocalizedException | NotFoundException $e) {
                        //
                    }
                }
            }

            $result = $this->processPlaceholders($result);
        }

        if ($this->config->isCriticalCssEnable()
            && $this->config->isUseCssCriticalPathEnable()
        ) {
            $configContent = $this->config->getDefaultCriticalCss();

            $result .= $configContent;
        }

        return $result;
    }

    /**
     * @param string $styles
     * @return string
     */
    private function processPlaceholders($styles)
    {
        $styles = (string) $styles;
        if (strpos($styles, '{pagespeed_') === false) {
            return $styles;
        }

        return strtr($styles, [
            '{pagespeed_asset_url}' => $this->getAssetUrl(),
            // '{pagespeed_static_url}' => $this->getStaticUrl(),
            // '{pagespeed_locale}' => $this->getLocale(),
        ]);
    }

    /**
     * @return array
     */
    private function getAssetGroups()
    {
        return [
            [
                'default.css',
            ],
            [
                // first match will be used only
                $this->getFullActionName(). '-' . $this->getCurrentPageLayout() . '.css',
                $this->getFullActionName() . '.css',
            ],
        ];
    }

    /**
     * @return string
     */
    private function getAssetUrl()
    {
        $context = $this->assetRepo->getStaticViewFileContext();

        return $this->getStaticUrl() . '/'
            . $context->getAreaCode() . '/'
            . ($context->getThemePath() ? $context->getThemePath() : '') . '/'
            . $this->getLocale();
    }

    /**
     * @return string
     */
    private function getStaticUrl()
    {
        $url = $this->url->getBaseUrl([
            '_type' => \Magento\Framework\UrlInterface::URL_TYPE_STATIC
        ]);

        return str_replace(['http://', 'https://'], '//', rtrim($url, '/'));
    }

    /**
     * @return string
     */
    private function getLocale()
    {
        return $this->localeResolver->getLocale();
    }
}
