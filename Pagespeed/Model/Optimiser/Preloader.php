<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class Preloader
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string|null
     */
    private $baseUrlHost;

    /**
     * @param Config $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(Config $config, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @param ResponseHttp $response
     * @param array|string $links
     * @param string $as
     * @param string $rel
     * @param string $type
     * @return ResponseHttp
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function push(ResponseHttp $response, $links, $as = 'style', $rel = 'preload', $type = '')
    {
        if ($this->config->isLinkPreloadEnabled()) {
            $response = $this->addLinkPreloadAndPrefetch($response, $links, $as/*, $rel*/);
        }

        if ($this->config->isHTTP2ServerPushEnabled()) {
            $response = $this->addHTTP2LinkHeaders($response, $links, $as, $rel, $type);
        }

        return $response;
    }

    /**
     * @return false|string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getBaseUrlHost()
    {
        if ($this->baseUrlHost === null) {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            $this->baseUrlHost = $this->getHost($baseUrl);
        }

        return $this->baseUrlHost;
    }

    /**
     * @param $src string
     * @return bool
     */
    private function isThirdPartySource($src)
    {
        if (filter_var($src, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        $host = $this->getHost($src);

        return $host !== $this->getBaseUrlHost();
    }

    /**
     * insteadof parse_url($url, PHP_URL_HOST)
     *
     * @param string $url
     * @return mixed
     */
    private function getHost($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
             return;
        }

        $uri = \Laminas\Uri\UriFactory::factory($url);
        return $uri->getHost();
    }

    /**
     * @param ResponseHttp $response
     * @param array|string $links
     * @param string $as
     * @param string $rel
     * @param string $type
     * @return ResponseHttp
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addHTTP2LinkHeaders(ResponseHttp $response, $links, $as = 'style', $rel = 'preload', $type = '')
    {
        if ($as === 'script' && !$this->config->isHTTP2ServerPushForCssEnabled()
            || $as === 'style' && !$this->config->isHTTP2ServerPushForCssEnabled()
            || $as === 'image' && !$this->config->isHTTP2ServerPushForImgEnabled()
        ) {
            return $response;
        }

        if (is_string($links)) {
            $links = [$links];
        }
        if (!empty($type)) {
            $type = '; type=' . $type;
        }

        $headerValue = [];
        $replaceHeader = true;
        //https://stackoverflow.com/questions/686217/maximum-on-http-header-values
        $headerValueLength = 0;
        $headerValueMaxLength = 1024 * 4;
        $header = $response->getHeader('Link');
        if ($header) {
            $headerValue[] = $header->getFieldValue();
        }

        $baseUrlHost = (string) $this->getBaseUrlHost();
        foreach ($links as $link) {
            if (empty($link) || strpos($link, $baseUrlHost) === false) {
                continue;
            }
            $_headerValue = "<" . $link . ">; rel={$rel}; as={$as}" . $type;
            $headerValueLength += strlen($_headerValue) + 2;
            if ($headerValueLength > $headerValueMaxLength) {
                break;
            }
            $headerValue[] = $_headerValue;
        }
        $headerValue = implode(', ', $headerValue);
        $response->setHeader('Link', $headerValue, $replaceHeader);

        return $response;
    }

    /**
     * @param ResponseHttp $response
     * @param array|string $links
     * @param string       $as
     * @return ResponseHttp
     */
    private function addLinkPreloadAndPrefetch(ResponseHttp $response, $links, $as = 'style')
    {
        $html = $response->getBody();
        if (empty($html)) {
            return $response;
        }
        if (is_string($links)) {
            $links = [$links];
        }
        $html = (string) $html;

        $_html = '';
        $relOffset = 2;
        $counter = 0;
        // crossorigin="anonymous" add warning and more requests
        // but increase score. why???
//        $forceAddCrossorigin = true;
        foreach ($links as $link) {
            $rel = 'preload';
            $crossorigin = '';
            if (/*$forceAddCrossorigin || */$this->isThirdPartySource($link)) {
                $crossorigin = ' crossorigin="anonymous" ';
            }
            if ($counter >= $relOffset
                || $as === 'image'
//                || $as === 'script'
//                || $as === 'style'
            ) {
                $rel = 'prefetch';
            }
            $_html .= '<link rel="' . $rel . '" as="' . $as . '" ' . $crossorigin . 'href="' . $link . '" />';
            $counter++;
        }

        $needle = '</head>';
        $pos = strpos($html, $needle);
        if ($pos !== false) {
            $html = substr_replace($html, $_html . $needle, $pos, strlen($needle));
            $response->setBody($html);
        }

        return $response;
    }
}
