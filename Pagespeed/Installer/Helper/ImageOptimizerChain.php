<?php

namespace Swissup\Pagespeed\Installer\Helper;

use Spatie\ImageOptimizer\OptimizerChainFactory;
use Swissup\Pagespeed\Image\Optimizers\Cwebp;
use Symfony\Component\Process\ExecutableFinder;

class ImageOptimizerChain
{
    /**
     * @var \Swissup\Pagespeed\Service\CheckImageOptimizerExisting
     */
    private $checker;

    /**
     *
     * @param \Swissup\Pagespeed\Service\CheckImageOptimizerExisting $checker
     */
    public function __construct(\Swissup\Pagespeed\Service\CheckImageOptimizerExisting $checker)
    {
        $this->checker = $checker;
    }

    /**
     * @param array $request
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(array $request)
    {
        return $this->checker->isAllExecutable();
    }
}
