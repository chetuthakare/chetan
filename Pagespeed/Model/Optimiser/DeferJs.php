<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class DeferJs extends AbstractOptimiser
{
    /**
     *
     * @var \Swissup\Pagespeed\Model\Optimiser\Preloader
     */
    private $preloader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string|null
     */
    private $baseUrlHost;

    /**
     * @param Config $config
     * @param \Swissup\Pagespeed\Model\Optimiser\Preloader $preloader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        \Swissup\Pagespeed\Model\Optimiser\Preloader $preloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($config);
        $this->preloader = $preloader;
        $this->storeManager = $storeManager;
    }

    /**
     *
     * @return string
     */
    public function getDelayScriptType()
    {
        return 'text/defer-javascript';
    }

    /**
     *
     * @return string
     */
    public function getUnpackScript()
    {
        return
        "<script type=\"text/javascript\">(function (_window, _document){
    /*_window.addEventListener('load', function() {*/
        var _scripts = _document.getElementsByTagName(\"script\"),
            _deferType = \"" . $this->getDelayScriptType() . "\",
            _isChrome = /Chrome/.test(_window.navigator.userAgent) && /Google Inc/.test(_window.navigator.vendor),
            /*_idleCallback = _window.requestIdleCallback || _window.setTimeout,*/
            _textNode;

        for(var i=0,l=_scripts.length;i<l;i++){
            var _type = _scripts[i].getAttribute(\"type\");
            if (_type && _type.toLowerCase() === _deferType && _scripts[i].parentNode) {
                /*_idleCallback(function() {*/
                    var _script = _scripts[i];
                    var parentNode = _script.parentNode;
                    try {
                        parentNode.replaceChild((function(sB){
                            if (!_isChrome) {
                                _textNode = _document.createTextNode(sB.innerHTML);
                                sB = _document.createElement('script');
                                sB.appendChild(_textNode);
                            }

                            sB.type = 'text/javascript';
                            /*if (_type.toLowerCase() == _deferXMageInitType) {
                                sB.type = 'text/x-magento-init';
                            }*/
                            return sB;

                        })(_script), _script);
                    } catch(e) {
                        console.error(e.name);
                        console.error(e.message);
                        console.error(e.stack);
                        console.error(_script);
                        throw e;
                    }
                /*});*/
            }
        }
    /*});*/
})(window, document);
</script>";
    }

    /**
     *
     * @return array
     */
    private function getIgnoreWithAttributes()
    {
        return [
            'data-defer-js-ignore' => null,
            'po_cmp_ignore' => null,
            // 'data-role' => "msrp-popup-template"
        ];
    }

    /**
     *
     * @param  \DOMElement $node
     * @return boolean
     */
    private function hasIgnoreAttribute($node)
    {
        $hasAttribute = false;
        $attributes = $this->getIgnoreWithAttributes();
        foreach ($attributes as $attribute => $attributeValue) {
            $_hasAttribute = $node->hasAttribute($attribute);
            if ($attributeValue !== null) {
                $_hasAttribute = $_hasAttribute
                    && ($node->getAttribute($attribute) == $attributeValue);
            }
            $hasAttribute = $hasAttribute || $_hasAttribute;
        }

        return $hasAttribute;
    }

    /**
     *
     * @return array
     */
    private function getIgnoreWithSignatures()
    {
        $signatures = $this->config->getDeferJsIgnores();

        $signatures = array_merge($signatures, [
            'type="text/x-magento-template"'
        ]);
        foreach ($this->getIgnoreWithAttributes() as $attributeName => $attributeValue) {
            if (is_string($attributeValue)) {
                $signatures[] = ' ' . $attributeName . '="' . $attributeValue . '"';
            } else {
                $signatures[] = ' ' . $attributeName;
            }
        }
        $signatures = array_filter($signatures);
        $signatures = array_unique($signatures);

        return $signatures;
    }

    /**
     *
     * @param  $scriptString string
     * @return boolean
     */
    private function hasIgnoreSignature($scriptString)
    {
        $signatures = $this->getIgnoreWithSignatures();
        $hasSignature = false;
        foreach ($signatures as $signature) {
            $hasSignature = $hasSignature || strpos($scriptString, (string) $signature) !== false;
        }

        return $hasSignature;
    }

    /**
     * @return string
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
     * @param $src string
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function isThirdPartyScript($src)
    {
        if (filter_var($src, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        $host = $this->getHost($src);

        return $host !== $this->getBaseUrlHost();
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$this->config->isDeferJsEnable() || $response === null) {
            return $response;
        }
        $html = (string) $response->getBody();

        if (strpos($html, '</body>') === false) {
            return $response;
        }

        $dom = $this->getDomDocument($html);
        $xpath = new \DOMXPath($dom);

        $scripts = $hrefSrc = [];
        $xpathString = '//script';
        $isUnpackScript = $this->config->isDeferJsUnpackEnable();
        $delayscriptType = $this->getDelayScriptType();
        $i = 0;
        $jsCounter = 0;
//        $delaySource = false;
        $offset = $this->config->isJsMergeEnable() ? 2 : 5;
        while ($node = $xpath->query($xpathString)->item($i)) {
            $tempDom = new \DOMDocument();
            /** @var \DOMElement $clonedNode */
            $clonedNode = $node->cloneNode(true);

            if ($this->hasIgnoreAttribute($clonedNode)) {
                 $i++;
                 continue;
            }

            $src = $clonedNode->getAttribute('src');
            if (!empty($src)) {
                $hrefSrc[] = $src;
            }
            $type = $clonedNode->getAttribute('type');
            if (empty($type)) {
                $clonedNode->setAttribute('type', 'text/javascript');
                $type = $clonedNode->getAttribute('type');
            }

            if ($type === 'text/javascript') {
                if ((empty($src) /*|| $delaySource*/)
                    && $isUnpackScript
                    && $jsCounter >= $offset
                ) {
                    $clonedNode->setAttribute('type', $delayscriptType);
                    $clonedNode->setAttribute('async', 'async');
                    $clonedNode->setAttribute('defer', 'defer');
                }

                if (!empty($src) && $this->isThirdPartyScript($src)
                    && $jsCounter > 1 // || strpos($src, '/require') === false
                ) {
                    $clonedNode->setAttribute('defer', 'defer');
                } elseif (!empty($src) && $jsCounter > 3) {
                    $clonedNode->setAttribute('async', 'async');
                }

//                $clonedNode->setAttribute('crossorigin', 'anonymous');
            }

            $tempDom->appendChild($tempDom->importNode($clonedNode, true));
            $_script = $tempDom->saveHTML();
            if ($this->hasIgnoreSignature($_script)) {
                $i++;
                continue;
            }
            if ((strstr($_script, 'x-magento') || strstr($_script, 'text/x-custom-template'))
                && strstr($_script, '<\/')
                && strstr($_script, '<\/script>') === false
            ) {
                $_script = str_replace('<\/', '</', $_script);
            }
            $scripts[] = $_script;
            // $scripts[] = $this->getSaveHTML($tempDom);

            $node->parentNode->removeChild($node);
            $jsCounter++;
        }

        // Remove 'all' scripts from raw HTML
        $html = str_replace($scripts, '', $html);
        // remove twice because saved php html !== real html markup
        $regExp = '/<script\b[^>]*>.*?<\/script>/is';
        $matches = [];
        preg_match_all($regExp, $html, $matches);
        $allScripts = [];
        if ($i === 0) {
            $allScripts = $matches[0];
        } else {
            foreach ($matches[0] as $mscript) {
                if ($i !== 0 && $this->hasIgnoreSignature($mscript)) {
                    continue;
                }
                $allScripts[] = $mscript;
            }
        }
        $html = str_replace($allScripts, '', $html);

        // $html = $this->getSaveHTML($dom);
        $scripts = implode("", $scripts);
        //console.log('some ø, æ, å characters')
        // $scripts = $this->getUtf8ToHtml($scripts);

        if ($isUnpackScript) {
            $unpackScript = $this->getUnpackScript();
            $scripts .= $unpackScript;
        }

        $html = str_replace('</body>', $scripts . '</body>', $html);
        $response->setBody($html);

        $this->preloader->push($response, $hrefSrc, 'script', 'preload');
        return $response;
    }
}
