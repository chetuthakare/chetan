<?php

namespace Swissup\Pagespeed\Block\Adminhtml\System\Config\Form\Field;

use Swissup\Codemirror\Block\Adminhtml\System\Config\Form\Field\Editor;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Critical extends Editor
{
    /**
     * @var string
     */
    protected $_template = 'Swissup_Codemirror::form/field/editor.phtml';

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * GettingStarted constructor.
     *
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = [
            'editor_config' => [
                'mode' => 'css',
                'line_wrapping' => true,
                'css_class' => 'cm-break-word'
            ]
        ]
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return string
     */
    protected function getStoreBaseUrl()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);

        return $store->getBaseUrl();
    }

    /**
     * Retrieve element HTML markup
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $url = $this->getStoreBaseUrl();
        $apiUrl = 'http://pagespeed.swissuplabs.com/critical-css/generate?website=' . urlencode($url);

        return parent::_getElementHtml($element) . '<a href="' . $apiUrl . '" target="_blank" rel="noopener">Get your site critical css.</a>';
    }
}
