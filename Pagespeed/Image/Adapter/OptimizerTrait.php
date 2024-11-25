<?php
namespace Swissup\Pagespeed\Image\Adapter;

use Swissup\Pagespeed\Helper\Config;
use Spatie\ImageOptimizer\OptimizerChain;
use Swissup\Pagespeed\Image\Optimizers\OptimizerChainFactory;

trait OptimizerTrait
{
    use ConfigHelperTrait; // override __counsruct for di ConfigHelper

    /**
     *
     * @var \Spatie\ImageOptimizer\OptimizerChain|null
     */
    protected $optimizerChain;

    /**
     *
     * @param  string $destination
     * @param  string $newName
     * @return string
     */
    abstract protected function _prepareDestination($destination = null, $newName = null);

    /**
     *
     * @param  string $filename
     * @return void
     */
    public function optimize($filename)
    {
        if ($this->configHelper->isImageOptimizerEnable()) {
            try {
                $this->getOptimizerChain()->optimize($filename);
            } catch (\Symfony\Component\Process\Exception\RuntimeException $e) {
                /** @var \Psr\Log\LoggerInterface|boolean $logger */
                $logger = $this->getLogger();
                if ($logger) {
                    $logger->critical($e->getMessage());
                }
            }
        }
    }

    /**
     * @return \Psr\Log\LoggerInterface|false
     */
    private function getLogger()
    {
        if ($this->configHelper->useLoggingUntilImageOptimise() === false) {
            return false;
        }
        $logger = false;
        /** @var \Psr\Log\LoggerInterface|null $logger */
        if (isset($this->logger)) {
            $logger = $this->logger;
        }

        return $logger;
    }

    /**
     *
     * @return \Spatie\ImageOptimizer\OptimizerChain
     */
    protected function getOptimizerChain()
    {
        if (!$this->optimizerChain) {

            $optimizersConfig = [];
            if ($this->configHelper->isWebPEnable()) {
                $optimizersConfig['convert_to_webp'] = true;
            }
            if ($this->configHelper->isImageOptimizersRemote()) {
                $optimizersConfig['remote'] = true;
                $optimizersConfig['apiUrl'] = $this->configHelper->getImageOptimizeServiceAPIUrl();
                $optimizersConfig['apiKey'] = $this->configHelper->getImageOptimizeServiceAPIKey();
                $optimizersConfig['baseUrl'] = $this->configHelper->getBaseUrl();
                $optimizersConfig['mediaDir'] = $this->configHelper->getMediaDir();
            }
            /** @var \Spatie\ImageOptimizer\OptimizerChain $optimizerChain */
            $optimizerChain = OptimizerChainFactory::create($optimizersConfig);
            $this->optimizerChain = $optimizerChain;

            $timeout = $this->configHelper->getImageOptimizerTimeout();
            $optimizerChain->setTimeout($timeout);

            /** @var \Psr\Log\LoggerInterface|boolean $logger */
            $logger = $this->getLogger();
            if ($logger) {
                $optimizerChain->useLogger($logger);
            }

            $this->optimizerChain = $optimizerChain;
        }
        return $this->optimizerChain;
    }
}
