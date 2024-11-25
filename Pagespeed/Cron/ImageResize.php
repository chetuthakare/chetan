<?php

namespace Swissup\Pagespeed\Cron;

class ImageResize
{
    const MAX_LIMIT = 10000;

    /**
     *
     * @var \Swissup\Pagespeed\Service\ImageResize
     */
    private $service;

    /**
     *
     * @var \Swissup\Pagespeed\Helper\Config
     */
    private $configHelper;

    /**
     * @param \Swissup\Pagespeed\Service\ImageResize $service
     * @param \Swissup\Pagespeed\Helper\Config $configHelper
     */
    public function __construct(
        \Swissup\Pagespeed\Service\ImageResize $service,
        \Swissup\Pagespeed\Helper\Config $configHelper
    ) {
        $this->service = $service;
        $this->configHelper = $configHelper;
    }

    /**
     * Run the warm cache process.
     * @return $this
     */
    public function execute()
    {
        if ($this->configHelper->isCronEnabled()) {

            $limit = $this->getLimit();

            $this->service->setLimit($limit);
            $generators = [
                $this->service->resizeCustomImages(),
                $this->service->resizeAllProductImages()
            ];

            foreach ($generators as $label => $generator) {
                $generator->current();
                for (; $generator->valid(); $generator->next()) {
                    $generator->key();
                }
            }
        }

        return $this;
    }

    /**
     *
     * @return int
     */
    private function getLimit()
    {
        $limit = $this->configHelper->getCronLimit();
        if ($limit > self::MAX_LIMIT || $limit < 1) {
            $limit = self::MAX_LIMIT;
        }

        return $limit;
    }
}
