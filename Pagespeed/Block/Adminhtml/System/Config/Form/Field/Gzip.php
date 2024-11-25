<?php

namespace Swissup\Pagespeed\Block\Adminhtml\System\Config\Form\Field;

use Swissup\Pagespeed\Block\Adminhtml\System\Config\Form\Field\StoreAbstract as Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Gzip extends Field
{
    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    private $curlFactory;

    /**
     * GettingStarted constructor.
     *
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        array $data = []
    ) {
        $this->curlFactory = $curlFactory;
        parent::__construct($context, $storeManager, $data);
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $url = $this->getStoreBaseUrl();
        $url = str_replace('http://', 'https://', $url);

        $note = '';
        if ((function_exists('ob_gzhandler') && ini_get('zlib.output_compression'))
            || (function_exists('apache_get_modules') && count(array_intersect(['mod_deflate', 'mod_gzip'], apache_get_modules())) > 0)
        ) {
            $message = 'GZIP is enabled';
            $cssClass = 'message-success';
        } else {
            $client = $this->curlFactory->create();
            $client->setOptions([
                CURLOPT_TIMEOUT => 120,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_NOBODY         => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_ENCODING => 'gzip',
            ]);
            try {
                $client->write(\Laminas\Http\Request::METHOD_GET, $url);

                $responseBody = $client->read();
                $responseBody = str_replace('HTTP/2', 'HTTP/1.1', $responseBody); //fix for h2 and old zend1
//                $headers = \Zend_Http_Response::extractHeaders($responseBody);
                $headers = \Laminas\Http\Headers::fromString($responseBody)->toArray();
                $client->close();
            } catch (\Laminas\Http\Exception\RuntimeException $e) {
                $headers = [];
            }

            $headerKey = 'content-encoding';
            if (isset($headers[$headerKey]) && (
                strpos((string) $headers[$headerKey], 'gzip') === 0
                    || strpos((string) $headers[$headerKey], 'deflate') !== false)
            ) {
                $message = 'GZIP is enabled';
                $cssClass = 'message-success';
            } elseif ($client->getErrno()) {
                $message = 'Cannot test GZIP';
                $note = $client->getError();
                $cssClass = 'message-error';
            } else {
                $message = 'Cannot test GZIP';
                $note = 'Check your server manually';
                $cssClass = 'message-error';
            }
        }

        $apiUrl = 'https://www.giftofspeed.com/gzip-test/';
        return '<a href="' . $apiUrl . '" class="message ' . $cssClass . '">' . $message . '</a>' .
        ($note ? '<p class="message note"><span>' . $note . '</span></p>' : '');
    }
}
