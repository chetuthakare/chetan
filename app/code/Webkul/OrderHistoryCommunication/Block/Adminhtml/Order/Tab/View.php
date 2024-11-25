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
namespace Webkul\OrderHistoryCommunication\Block\Adminhtml\Order\Tab;

use \Webkul\OrderHistoryCommunication\Api\OrderHistoryCommunicationRepositoryInterface;

class View extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'tab/view/orderCommunication.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunicationFactory $orderHistory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Filesystem\Io\File $filesystemFile
     * @param \Webkul\OrderHistoryCommunication\Helper\Data $helper
     * @param array $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunicationFactory $orderHistory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Filesystem\Io\File $filesystemFile,
        \Webkul\OrderHistoryCommunication\Helper\Data $helper,
        OrderHistoryCommunicationRepositoryInterface $orderRepo,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->orderHistory = $orderHistory;
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
        $this->filesystemFile = $filesystemFile;
        $this->helper = $helper;
        $this->orderRepo = $orderRepo;
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

    /**
     * Get submit url
     *
     * @return string|true
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('orderhistorycommunication/order/save', ['order_id' => $this->getOrderId()]);
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
}
