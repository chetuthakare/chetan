<?php

namespace Swissup\Pagespeed\Installer\Command;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

use Spatie\ImageOptimizer\OptimizerChainFactory;
use Symfony\Component\Process\ExecutableFinder;
use Swissup\Pagespeed\Image\Optimizers\Cwebp;

use Swissup\Pagespeed\Installer\Command\Traits\LoggerAware;

class CheckImageOptimizers
{
    use LoggerAware;

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
     * @param \Swissup\Marketplace\Installer\Request $request
     * @return void
     */
    public function execute($request)
    {
        if (!$this->checker->isAllExecutable()) {
            $this->getLogger()->warning($this->checker->getMainMessage());
        }
    }
}
