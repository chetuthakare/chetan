<?php
declare(strict_types=1);

namespace Swissup\Pagespeed\Service;

use Magento\Framework\UrlInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CriticalCss
{
    const API_URL = 'http://pagespeed.swissuplabs.com/critical-css/generate';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface|null
     */
    private $store;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    private $curlFactory;

    /**
     * @var string|null
     */
    private $criticalCss = '';

    /**
     * Backend Config Model Factory
     *
     * @var \Magento\Config\Model\Config\Factory
     */
    private $configFactory;

    /**
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     * @param \Magento\Config\Model\Config\Factory $configFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Config\Model\Config\Factory $configFactory
    ) {
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->curlFactory = $curlFactory;
        $this->configFactory = $configFactory;
    }

    /**
     *
     * @param $store
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getTargetUrls($store)
    {
        $targetUrls = [];
        $targetUrls[] = $store->getBaseUrl(UrlInterface::URL_TYPE_WEB, true);

        $categoryId = $store->getRootCategoryId();
        if ($categoryId) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->categoryRepository->get($categoryId, $store->getId());
            $targetUrls[] = $category->getUrl();

            $product = $category->getProductCollection()->getLastItem();
            if ($product->getId()) {
                $targetUrls[] = $product->getProductUrl();
            }
        }

        foreach ($targetUrls as &$targetUrl) {
//            $targetUrl = str_replace( 'magento240.local', 'swissupdemo.com/pagespeed/current/', $targetUrl);
            $targetUrl = urlencode($targetUrl);
        }

        return $targetUrls;
    }

    /**
     *
     * @param $targetUrls
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCriticalCssByUrls($targetUrls)
    {
        $criticalCss = '';
        $websiteParam  = implode(',', $targetUrls);

        /** @var \Magento\Framework\HTTP\Client\Curl $client */
        $client = $this->curlFactory->create();
        try {
            $apiUrl = self::API_URL . "?website=" . $websiteParam . '&ignore-urls=1';
            $client->get($apiUrl);
            $status = $client->getStatus();
            if ($status === 200) {
                $criticalCss = $client->getBody();
            } else {
                throw new \Magento\Framework\Exception\RemoteServiceUnavailableException(
                    __('Service Api Error %1', $status)
                );
            }
        } catch (\Laminas\Http\Exception\RuntimeException $e) {
            $criticalCss = '';
            throw new \Magento\Framework\Exception\RemoteServiceUnavailableException(
                __('Service Api Error %1', $e->getMessage())
            );
        }
        $this->criticalCss = $criticalCss;

        return $criticalCss;
    }

    /**
     * @param $store
     * @return $this
     */
    public function setStore($store)
    {
        if (is_int($store) || is_string($store)) {
            $store = $this->storeManager->getStore($store);
        }
        if (!$store instanceof \Magento\Store\Api\Data\StoreInterface) {
            throw new \Magento\Framework\Exception\InvalidArgumentException(
                __('store argument should be inctabceof StoreInterface')
            );
        }

        $this->store = $store;
        $this->criticalCss = '';

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generate()
    {
        if (empty($this->store)) {
            throw new \Magento\Framework\Exception\InvalidArgumentException(
                __('Set store before')
            );
        }
        $urls = $this->getTargetUrls($this->store);
        $this->getCriticalCssByUrls($urls);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriticalCss()
    {
        return $this->criticalCss;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function saveConfig($criticalCss = '')
    {
        if (empty($criticalCss)) {
            $criticalCss = (string) $this->criticalCss;
        }
        if (empty($criticalCss)) {
            throw new \Magento\Framework\Exception\InvalidArgumentException(
                __('Critical Css is empty')
            );
        }

        $store = $this->store;
        $storeId = $this->store ? $store->getId() : null;

        $configData = [
            'section' => 'pagespeed',
            'website' => $storeId ? $store->getWebsiteId() : null,
            'store' => $storeId ? $store->getId() : null,
            'groups' => [
                'css' => [
                    'groups' => [
                        'critical' => [
                            'fields' => [
                                'enable' => [
//                                        \Swissup\Pagespeed\Helper\Config::CONFIG_XML_PATH_CSS_CRITICAL_ENABLE
                                    'value' => true
                                ],
                                'default' => [
//                                        \Swissup\Pagespeed\Helper\Config::CONFIG_XML_PATH_CSS_CRITICAL_DEFAULT
                                    'value' => $criticalCss
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        /** @var \Magento\Config\Model\Config $configModel */
        $configModel = $this->configFactory->create(['data' => $configData]);
        $configModel->save();

        return $this;
    }
}
