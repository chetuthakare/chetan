<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class DeferCss extends AbstractOptimiser
{
//    /**
//     *
//     * @var \Swissup\Pagespeed\Model\Optimiser\Preloader
//     */
//    private $preloader;
//
//    /**
//     * @param Config $config
//     * @param \Swissup\Pagespeed\Model\Optimiser\Preloader $preloader
//     */
//    public function __construct(
//        Config $config,
//        \Swissup\Pagespeed\Model\Optimiser\Preloader $preloader
//    ) {
//        parent::__construct($config);
//        $this->preloader = $preloader;
//    }

    /**
     * Css loader script that minimize layout shifts during style-m and styles-l loading
     *
     * @return string
     */
    public function getCssLoaderScript()
    {
        $script = <<<JS
<script data-defer-js-ignore>
var pagespeedCss = {
    ready: window.matchMedia && !window.matchMedia('(min-width: 768px)').matches,
    timer: false,
    pending: window.pagespeedStyles || [],
    load: function (style) {
        style.onload = null;

        if (this.ready || style.media === 'print') {
            style.rel = 'stylesheet';
            return;
        }

        this.pending.push(style);

        if (style.href && style.href.indexOf('styles-l') > -1) {
            this.loadAll();
        } else if (this.pending.length) {
            this.timer = setTimeout(this.loadAll.bind(this), 300);
        }
    },
    loadPending: function () {
        this.pending.map(function (style) {
            this.load(style);
        }, this);
    },
    loadAll: function () {
        if (this.timer) {
            clearTimeout(this.timer);
        }
        this.ready = true;
        this.loadPending();
    }
};
pagespeedCss.loadPending();
</script>
JS;

        return preg_replace(
            [
                '/\/\/.*/', // remove comments
                '/\n/',     // remove newlines
                '/\s+/'     // remove trailing spaces
            ],
            ['', '', ' '],
            $script
        );
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$response || !$this->config->isCriticalCssEnable()) {
            return $response;
        }

        $html = (string) $response->getBody();

        $dom = $this->getDomDocument($html);
        $xpath = new \DOMXPath($dom);

        $infoLinks = [];
        $useCssLoader = strpos($html, '/css/styles-l') !== false;
        $xpathString = '//link[@rel="stylesheet" or @media="all" or @media="screen"]';
        $nodes = $xpath->query($xpathString);
        foreach ($nodes as $node) {
            $link = $this->getNodeString($node);
            $relAttr    = $node->getAttribute('rel');
            $typeAttr   = $node->getAttribute('type');
            $mediaAttr  = $node->getAttribute('media');
            $onloadAttr = $node->getAttribute('onload');
            $hrefAttr   = $node->getAttribute('href');

            $node->setAttribute('rel', 'preload');
            $node->setAttribute('as', 'style');
            $node->removeAttribute('type');

            if ($mediaAttr === 'print' && strpos($onloadAttr, "this.media=") !== false) {
                preg_match("/this\.media='(.*)?'/", $onloadAttr, $matches);
                if (!empty($matches[1])) {
                    $mediaAttr = $matches[1];
                    $node->setAttribute('media', $mediaAttr);
                }
            }

            if ($mediaAttr !== 'print') {
                if (!$useCssLoader) {
                    $node->setAttribute('onload', "this.onload=null;this.rel='stylesheet'");
                } else {
                    $node->setAttribute(
                        'onload',
                        'window.pagespeedStyles = window.pagespeedStyles || []; ' .
                        'window.pagespeedCss ? window.pagespeedCss.load(this) : window.pagespeedStyles.push(this)'
                    );
                }
            }

            $to = $this->getNodeString($node);
            $to = trim($to, "\r\n ");
            $to = str_replace('>', ' />', $to);

            $regex = '/<link\n?.*' .
                // 'rel="'   . preg_quote($relAttr)       . '".+' .
                // 'type="'  . preg_quote($typeAttr, '/') . '".+' .
                // 'media="' . preg_quote($mediaAttr)     . '".+' .
                'href="'  . preg_quote($hrefAttr, '/') . '".*?>/i';

            $infoLinks[] = [
                'rel'   => $relAttr,
                'type'  => $typeAttr,
                'media' => $mediaAttr,
                'href'  => $hrefAttr,
                'from'  => $link,
                'to'    => $to,
                'regex' => empty($hrefAttr) ? false : $regex
            ];
        }

        $regExp = '/<link\b[^>]*>/is';
        $allLinkElementAtPage = [];
        preg_match_all($regExp, $html, $allLinkElementAtPage);
        foreach ($allLinkElementAtPage[0] as $linkElement) {
            foreach ($infoLinks as $i => $linkInfo) {
                $hrefPosition  = empty($linkInfo['href']) ? false : strpos($linkElement, (string) $linkInfo['href']);
                $relPosition   = empty($linkInfo['rel']) ? false : strpos($linkElement, (string) $linkInfo['rel']);
                $typePosition  = empty($linkInfo['type']) ? false : strpos($linkElement, (string) $linkInfo['type']);
                $mediaPosition = empty($linkInfo['media']) ? false : strpos($linkElement, (string) $linkInfo['media']);

                if ($hrefPosition > 5 &&
                   ($relPosition > 5 || strpos($linkElement, 'rel=') === false) &&
                   ($typePosition > 5 || strpos($linkElement, 'type=') === false) &&
                   ($mediaPosition > 5 || strpos($linkElement, 'media=') === false)
                ) {
                    $infoLinks[$i]['origin'] = $linkElement;
                }
            }
        }
        foreach ($infoLinks as $linkInfo) {
            if (isset($linkInfo['origin'])) {
                $html = str_replace($linkInfo['origin'], $linkInfo['to'], $html);
            } elseif (!empty($linkInfo['regex'])) {
                $html = preg_replace(
                    $linkInfo['regex'],
                    $linkInfo['to'],
                    $html
                );
            }
        }

        if ($useCssLoader) {
            $html = str_replace('</body>', $this->getCssLoaderScript() . '</body>', $html);
        }

        $response->setBody($html);

        return $response;
    }

    /**
     *
     * @param \DOMElement $node
     * @return string
     */
    protected function getNodeString($node)
    {
        $tempDom = new \DOMDocument();
        $clonedNode = $node->cloneNode(true);
        $tempDom->appendChild($tempDom->importNode($clonedNode, true));
        return $tempDom->saveHTML();
    }
}
