<?php

namespace Swissup\Pagespeed\ViewModel;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Init extends DataObject implements ArgumentInterface
{
    /**
     *
     * @var \Swissup\Pagespeed\Helper\Config
     */
    private $configHelper;

    /**
     * @param \Swissup\Pagespeed\Helper\Config $configHelper
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Swissup\Pagespeed\Helper\Config $configHelper
    ) {
        parent::__construct();
        $this->configHelper = $configHelper;
    }

    /**
     *
     * @return string
     */
    public function getJsLibsInit()
    {
        $libs = [];
        if ($this->configHelper->isCriticalCssEnable()) {
            $libs[] = 'cssrelpreload';
        }

        foreach ($libs as &$lib) {
            $lib = '"Swissup_Pagespeed/js/lib/' . $lib . '":{}';
        }

        return implode(',', $libs);
    }

    /**
     * @return bool
     */
    public function isForceRequireJsLoadingEnabled()
    {
        return $this->configHelper->isEnable();
    }
}
