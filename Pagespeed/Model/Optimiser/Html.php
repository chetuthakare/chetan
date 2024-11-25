<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Magento\Framework\Code\Minifier\Adapter\Css\CSSmin;

class Html extends AbstractOptimiser
{
    /**
     *
     * @var \Swissup\Pagespeed\Code\Minifier\HtmlFactory
     */
    protected $minifierFactory;

    /**
     * @param \Swissup\Pagespeed\Helper\Config $config
     * @param \Swissup\Pagespeed\Code\Minifier\HtmlFactory $minifierFactory
     */
    public function __construct(
        \Swissup\Pagespeed\Helper\Config $config,
        \Swissup\Pagespeed\Code\Minifier\HtmlFactory $minifierFactory
    ) {
        parent::__construct($config);
        $this->minifierFactory = $minifierFactory;
    }

    /**
     * Perform result postprocessing
     *
     * @param \Magento\Framework\App\Response\Http $response
     * @return \Magento\Framework\App\Response\Http
     */
    public function process(\Magento\Framework\App\Response\Http $response = null)
    {
        if (!$this->config->isContentMinifyEnable() || $response === null) {
            return $response;
        }
        $contentTypeHeader = $response->getHeader('content-type');
        if ($contentTypeHeader && $contentTypeHeader->getFieldValue() === 'text/plain') {
            return $response;
        }
        $html = (string) $response->getBody();
        if (empty($html)) {
            return $response;
        }

        $options = [];
        $isCssMinifier = $this->config->isContentCssMinifyEnable();
        if ($isCssMinifier) {
            $options['cssMinifier'] = ['Minify_CSS', 'minify'];
            // $options['cssMinifier'] = array('CSSmin', 'minify');
        }
        $isJsMinifier = $this->config->isContentJsMinifyEnable();
        if ($isJsMinifier) {
            $options['jsMinifier'] = ['JSMin\JSMin', 'minify'];
            // $options['jsMinifier'] = array('JShrink\Minifier', 'minify');
        }

        $minifier = $this->minifierFactory->create([
            'html' => (string) $html,
            'options' => $options
        ]);

        try {
            $_html = $minifier->process();
        } catch (\Exception $e) {
            throw $e;
        }

        $response->setBody($_html);

        return $response;
    }
}
