<?php

/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_OrderHistoryCommunication
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\OrderHistoryCommunication\Block\Order;

use Magento\Customer\Model\Context;

/**
 * Sales order view block
 *
 * @api
 * @since 100.0.2
 */
class Communication extends \Magento\Sales\Block\Order\Invoice\Items
{
    /**
     * @var string
     */
    protected $_template = 'order/communication.phtml';

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunicationFactory $orderHistory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Filesystem\Io\File $filesystemFile
     * @param \Webkul\OrderHistoryCommunication\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunicationFactory $orderHistory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Filesystem\Io\File $filesystemFile,
        \Webkul\OrderHistoryCommunication\Helper\Data $helper,
        array $data = []
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->httpContext = $httpContext;
        $this->orderHistory = $orderHistory;
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
        $this->filesystemFile = $filesystemFile;
        $this->helper = $helper;
        parent::__construct(
            $context,
            $registry,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(
            __('Order # %1', $this->getOrder()->getRealOrderId())
        );
        $infoBlocks = $this->paymentHelper->getInfoBlock($this->getOrder()->getPayment(), $this->getLayout());
        $this->setChild('payment_info', $infoBlocks);
    }

    /**
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl('*/*/history');
        }
        return $this->getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return \Magento\Framework\Phrase
     */
    public function getBackTitle()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return __('Back to My Orders');
        }
        return __('View Another Order');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getShipmentUrl($order)
    {
        return $this->getUrl('*/*/shipment', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getCreditmemoUrl($order)
    {
        return $this->getUrl('*/*/creditmemo', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $invoice
     * @return string
     */
    public function getPrintInvoiceUrl($invoice)
    {
        return $this->getUrl('*/*/printInvoice', ['invoice_id' => $invoice->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getPrintAllInvoicesUrl($order)
    {
        return $this->getUrl('*/*/printInvoice', ['order_id' => $order->getId()]);
    }
    /**
     * @param object $order
     * @return string
     */
    public function getCommunicationOrder()
    {
        $orderId =  $this->getOrder()->getId();
        $orderHistory = $this->orderHistory->create()->getCollection();
        $orderHistory->addFieldToFilter('order_id', $orderId);
        return $orderHistory;
    }

    /**
     * Get Media Url
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * check is image or link
     * @param string $imageName
     * @param int $queryId
     * @param int $commandId
     */
    public function isImage($imageName, $order_id)
    {
        $url = $this->helper->getImageMediaPath($imageName, $order_id);
        $imageCheck = !empty($url)?getimagesize($url):"false";
        if (is_array($imageCheck) && $imageCheck!==false) {
            return true;
        } else {
            return false;
        }
    }
}
