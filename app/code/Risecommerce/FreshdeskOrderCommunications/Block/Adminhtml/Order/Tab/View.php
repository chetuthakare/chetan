<?php

namespace Risecommerce\FreshdeskOrderCommunications\Block\Adminhtml\Order\Tab;

//use \Webkul\OrderHistoryCommunication\Api\OrderHistoryCommunicationRepositoryInterface;

class View extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'Risecommerce_FreshdeskOrderCommunications::tab/view/orderCommunication.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
  
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Filesystem\Io\File $filesystemFile
     
     * @param array $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
       // \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunicationFactory $orderHistory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Risecommerce\FreshdeskOrderCommunications\Helper\Data $helperdata,
       // \Magento\Framework\Filesystem\Io\File $filesystemFile,
        //\Webkul\OrderHistoryCommunication\Helper\Data $helper,
       // OrderHistoryCommunicationRepositoryInterface $orderRepo,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
      
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
        $this->helperdata = $helperdata;
        //$this->filesystemFile = $filesystemFile;
       // $this->helper = $helper;
       // $this->orderRepo = $orderRepo;
        $this->searchBuilder = $searchBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Get Order
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
     * Retrieve get CustomerEmail id
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
     * Retrieve order collection
     *
     * @return collection
     */
    public function getOrderData()
    {
         return $this->getOrder()->getAllVisibleItems();

         
    }

    
    /**
     * Get Tab Label
     */

    public function getTabLabel()
    {
        return __('Communication History');
    }

    /**
     * Get Tab Title
     */

    public function getTabTitle()
    {
        return __('Communication History');
    }
    
    /**
     * Can Show Tab
     */

    public function canShowTab()
    {
        return true;
    }

    /**
     * Check Is Hidden
     */

    public function isHidden()
    {
        return false;
    }
     /**
      * Preparing global layout
      *
      * @return $this
      */
    protected function _prepareLayout()
    {
        $this->addChild(
            'submit_button',
            \Magento\Backend\Block\Widget\Button::class,
            ['id' => 'submit_communication_button',
            'label' => __('Submit'),
            'class' => 'action-secondary save',
            'type'=>'submit']
        );
        return parent::_prepareLayout();
    }


 public function getSubmitUrl()
    {
        return $this->getUrl('ordercommunications/ordercommunications/reply', ['order_id' => $this->getOrderId()]);
    }
  

    /**
     * @param object $order
     * @return string
     */
    public function getCommunicationOrder()
    {
        $orderId =  $this->getOrderId();
        $searchCriteria = $this->searchBuilder->addFilter(
            'order_id',
            $orderId
        )->create();
        $items = $this->orderRepo->getList($searchCriteria);
        return $items;
    }

   


     /**
      * Status history date/datetime getter
      *
      * @param array $item
      * @param string $dateType
      * @param int $format
      * @return string
      */
    public function getItemCreatedAt($item, $dateType = 'date', $format = \IntlDateFormatter::MEDIUM)
    {
        if (!isset($item)) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->formatDate($item, $format);
        }
        return $this->formatTime($item, $format);
    }
    public function getallChat($ticket_id): bool
    {
        if ($this->getallChatData($ticket_id) != "") {
            return true;
        }
        return false;
    }

    public function getDataApiImage()
    {
        return $this->helperdata->getapiimage();
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
        $originalRequest = $data['description_text'];
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