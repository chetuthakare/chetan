<?php
namespace Swissup\Pagespeed\Image\Adapter;

use Magento\Framework\App\Filesystem\DirectoryList;
use Swissup\Pagespeed\Helper\Config;
use Spatie\ImageOptimizer\Optimizer;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;

trait ConfigHelperTrait
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * override \Magento\Framework\Image\Adapter\AbstractAdapter
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Swissup\Pagespeed\Helper\Config $configHelper
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Psr\Log\LoggerInterface $logger,
        \Swissup\Pagespeed\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->_filesystem = $filesystem;
        $this->logger = $logger;
        /** @var \Magento\Framework\Filesystem\Directory\Write $directoryWrite */
        $directoryWrite = $this->_filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->directoryWrite = $directoryWrite;
        $this->configHelper = $configHelper;
        $this->data = $data;
    }
}
