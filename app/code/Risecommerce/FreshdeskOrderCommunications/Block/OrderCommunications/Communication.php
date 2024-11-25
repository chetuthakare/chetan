<?php

namespace Risecommerce\FreshdeskOrderCommunications\Block\OrderCommunications;

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
    protected $_template = 'Risecommerce_FreshdeskOrderCommunications::communications.phtml';

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
    
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     
     
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Risecommerce\FreshdeskOrderCommunications\Helper\Data $helperdata,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,


        array $data = []
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->httpContext = $httpContext;
        $this->helperdata = $helperdata;
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;

        //  $this->helper = $helper;
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
     * Get Order Id
     */

    public function getOrderId() 
    {
        return $this->getOrder()->getEntityId();
    }

    /**
     * Get Order Increment Id
     */

    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }

    /**
     * Retrieve order increment id
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->getOrder()->getCustomerEmail();
    }

    /**
     * Retrieve get CustomerName
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->getOrder()->getCustomerName();
    }

    /**
     * Retrieve get Mobile Number
     *
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->getOrder()->getShippingAddress()->getTelephone();
    }



    /**
     * Retrieve order Email id
     *
     * @return collection
     */
    public function getOrderData()
    {
        return $this->getOrder()->getAllVisibleItems();
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
    public function getallChat($ticket_id): bool
    {
        if ($this->getallChatData($ticket_id) != "") {
            return true;
        }
        return false;
    }

    public function getDataStatus()
    {
        return $this->helperdata->getStatus();
    }

    public function getDataApiKey()
    {
        return $this->helperdata->getapiKey();
    }

    public function getDataApiPass()
    {
        return $this->helperdata->getapiPass();
    }
    
    public function getDataApiImage()
    {
        return $this->helperdata->getapiimage();
    }

    public function getDataApiDomain()
    {
        return $this->helperdata->getapiDomain();
    }

    public function getallChatData($ticket_id)
    {
        $api_key = $this->getDataApiKey();
        $domain = $this->getDataApiDomain();
        $conversations_endpoint = "https://{$domain}.freshdesk.com/api/v2/tickets/{$ticket_id}/conversations";
        $ch = curl_init($conversations_endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($api_key . ':x')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }


    public function getMainData($ticket_id)
    {
        $api_key = $this->getDataApiKey();
        $domain = $this->getDataApiDomain();
        $conversations_endpoint = "https://{$domain}.freshdesk.com/api/v2/tickets/{$ticket_id}";
        $ch = curl_init($conversations_endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($api_key . ':x')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }
        curl_close($ch);
        $data = json_decode($response, true);
        $originalRequest = $data;
        return $originalRequest;
    }
    public function getallTickets()
    {

        $api_key = $this->getDataApiKey();
        $domain = $this->getDataApiDomain();
        $endpoint = "https://$domain.freshdesk.com/api/v2/tickets";
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("$api_key:X")
        ));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }
        curl_close($ch);
        $tickets = json_decode($response, true);
        if ($tickets) {
            return $tickets;
            // foreach ($tickets as $ticket) {
            //     // echo "Ticket ID: " . $ticket['id'] . "</br>";
            //     // echo "Email ID: " . $ticket['custom_fields']['cf_email'] . "</br>";
            //     // echo "Subject: " . $ticket['subject'] . "</br>";
            //     // // You can display more ticket details here
            //     // echo "\n";
            // }
        } else {
            echo "No tickets found.";
        }
    }
	public function getuserdatabymail($email){
 
		$api_key = $this->getDataApiKey();
		$domain = $this->getDataApiDomain();
		$url = "https://$domain.freshdesk.com/api/v2/contacts?email=$email";

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, "$api_key:X");

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code === 200) {
			// User found
			$user = json_decode($response, true);
			return $user;
		}
		return "error";
	}
}
