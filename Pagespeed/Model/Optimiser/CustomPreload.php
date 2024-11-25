<?php
namespace Swissup\Pagespeed\Model\Optimiser;

use Swissup\Pagespeed\Helper\Config;
use Magento\Framework\App\Response\Http as ResponseHttp;

class CustomPreload extends AbstractOptimiser
{
    /**
     *
     * @var \Swissup\Pagespeed\Model\Optimiser\Preloader
     */
    private $preloader;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @param Config $config
     * @param \Swissup\Pagespeed\Model\Optimiser\Preloader $preloader
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     */
    public function __construct(
        Config $config,
        \Swissup\Pagespeed\Model\Optimiser\Preloader $preloader,
        \Magento\Framework\Filesystem\Io\File $ioFile
    ) {
        parent::__construct($config);
        $this->preloader = $preloader;
        $this->ioFile = $ioFile;
    }

    /**
     * Perform result postprocessing
     *
     * @param ResponseHttp $response
     * @return ResponseHttp
     */
    public function process(ResponseHttp $response = null)
    {
        if (!$response || !$this->config->isLinkPreloadEnabled()) {
            return $response;
        }

        $links = $this->config->getCustomLinkForPreload();
        $urlsByTypes = [];
        foreach ($links as $link) {

            if (!filter_var($link, FILTER_VALIDATE_URL)) {
                continue;
            }

            $extension = $this->getFileExtension($link);
            switch ($extension) {
                case 'js':
                    $urlsByTypes['script'][] = $link;
                    break;
                case 'css':
                    $urlsByTypes['style'][] = $link;
                    break;
                case 'eot':
                case 'otf':
                case 'ttf':
                case 'woff':
                case 'woff2':
                    $urlsByTypes['font'][] = $link;
                    break;
                case 'ico':
                case 'webp':
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'bmp':
                case 'svg':
                case 'png':
                    $urlsByTypes['image'][] = $link;
                    break;
            }
        }

        foreach ($urlsByTypes as $as => $urls) {
            $this->preloader->push($response, $urls, $as, 'preload');
        }

        return $response;
    }

    /** instead pathinfo($path, PATHINFO_EXTENSION);
     * @param $path
     * @return string
     */
    private function getFileExtension($path)
    {
        $pathInfo = $this->ioFile->getPathInfo($path);
        return isset($pathInfo['extension']) ? $pathInfo['extension'] : false;
    }
}
