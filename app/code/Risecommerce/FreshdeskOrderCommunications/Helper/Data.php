<?php

namespace Risecommerce\FreshdeskOrderCommunications\Helper;

use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\RequestInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MOD_ENABLE = 'ordercommunication/general_options/enable';
    const API_DOMAIN = 'ordercommunication/general_options/freshdeskdomain';
    const API_PASSWORD = 'ordercommunication/general_options/freshdeskpass';
    const API_KEY = 'ordercommunication/general_options/freshdeskapikey';
    const API_IMAGE = 'ordercommunication/general_options/image';

    protected $_storeManager;
    protected $assetRepo;
    protected $request;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Repository $assetRepo,
        RequestInterface $request
    ) {
        $this->_storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
        $this->request = $request;
        parent::__construct($context);
    }


    public function getStatus()
    {
        return $this->scopeConfig->getValue(
            self::MOD_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getapiKey()
    {
        return $this->scopeConfig->getValue(
            self::API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getapiPass()
    {
        return $this->scopeConfig->getValue(
            self::API_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getapiimage()
    {
        $chatIcon = $this->scopeConfig
            ->getValue('ordercommunication/general_options/image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!$chatIcon) {
            $params = ['_secure' => $this->request->isSecure()];
            return $this->assetRepo->getUrlWithParams('Risecommerce_WhatsAppChat::images/chat.jpeg', $params);
        }

        return $this->getMediaUrl(). 'custom/images/'.$chatIcon;
    }

    /**
     * Return media path
     *
     * @return string
     */

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getapiDomain()
    {
        return $this->scopeConfig->getValue(
            self::API_DOMAIN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
