<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class CriticalCss extends AbstractOptimiser
{
    /**
     *
     * @var \Swissup\Pagespeed\Block\Html\Header\CriticalCssFactory
     */
    private $criticalCssViewModelFactory;

    /**
     *
     * @var \Swissup\Pagespeed\Model\Css\Improver
     */
    private $cssImprover;

    /**
     * @param Config $config
     * @param \Swissup\Pagespeed\Block\Html\Header\CriticalCssFactory $criticalCssViewModelFactory
     * @param \Swissup\Pagespeed\Model\Css\Improver $cssImprover
     */
    public function __construct(
        Config $config,
        \Swissup\Pagespeed\Block\Html\Header\CriticalCssFactory $criticalCssViewModelFactory,
        \Swissup\Pagespeed\Model\Css\Improver $cssImprover
    ) {
        parent::__construct($config);
        $this->criticalCssViewModelFactory = $criticalCssViewModelFactory;
        $this->cssImprover = $cssImprover;
    }

    /**
     *
     * @return string|boolean
     */
    protected function getCriticalCss()
    {
        if ($this->config->isUseCssCriticalPathEnable()
         && $this->criticalCssViewModelFactory->isExist()
        ) {
            /** @var \Magento\Theme\Block\Html\Header\CriticalCss $viewModel */
            $viewModel = $this->criticalCssViewModelFactory->create();
            $styles = $viewModel->getCriticalCssData();
        } else {
            $styles = $this->config->getDefaultCriticalCss();
        }

        return $styles;
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
        $styles = $this->getCriticalCss();

        if (!empty($styles)) {
            $needle = '</title>';
            $pos = strpos($html, $needle);
            if ($pos !== false) {
                $html = str_replace($styles, '', $html);

                // compatibility with third-party modules that modify html output via DOMDocument
                // https://php.watch/versions/8.2/mbstring-qprint-base64-uuencode-html-entities-deprecated#html
                $html = str_replace(htmlspecialchars_decode(htmlentities($styles)), '', $html);

                $html = preg_replace(
                    '/<style type="text\/css" data-type="criticalCss">(\s|\n|\r|\t)*<\/style>/',
                    '',
                    $html
                );

                $needleCriticalCss = 'type="text/css" data-type="criticalCss"';
                $positionCriticalCss = strpos($html, $needleCriticalCss) - strlen('<style ');
                if ($positionCriticalCss > 0) {
                    $html = substr_replace(
                        $html,
                        '',
                        $positionCriticalCss,
                        // 5 - is a newline and 4 spaces
                        strlen('<style ') + strlen($needleCriticalCss) + 5 + strlen('></style>')
                    );
                } else {
                    $html = str_replace(' type="text/css" data-type="criticalCss"', '', $html);
                    $html = str_replace("<style></style>", '', $html);
                }

                $styles = '<style type="text/css" data-type="criticalCss">' . "\n"
                    . '    ' . $styles . "\n"
                    . '</style>';


                $styles = $this->cssImprover
                    ->setResponse($response)
                    ->process($styles);

                $html = substr_replace($html, $needle . $styles, $pos, strlen($needle));
                $response->setBody($html);
            }
        }

        return $response;
    }
}
