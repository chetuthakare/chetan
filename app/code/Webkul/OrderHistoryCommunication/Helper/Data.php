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
namespace Webkul\OrderHistoryCommunication\Helper;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunicationFactory;

/**
 * Webkul BarcodeInventory Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MOD_ENABLE = 'OrderHistoryCommunication/general_options/enable';
    const ORDER_COMMUNICATION_EMAIL = 'orderhistorycommunication/email/communicationmail';
    const ORDER_COMMUNICATION_EMAIL_ADMIN = 'orderhistorycommunication/email/admincommunicationmail';
    /**
     * @var \Webkul\BarcodeInventory\Model\Move
     */
    protected $moveGroup;

    /**
     * @param \Magento\Framework\App\Helper\Context  $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sales\Model\OrderFactory $order
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        OrderHistoryCommunicationFactory $orderHistoryCommunicationFactory,
        \Magento\Framework\Filesystem\Io\File  $file
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->order = $order;
        $this->customer = $customerRepositoryInterface;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->file = $file;
        $this->messageManager = $messageManager;
        $this->orderHistory = $orderHistoryCommunicationFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(
            DirectoryList::MEDIA
        );
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->scopeConfig->getValue(
            self::MOD_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Configuration Value
     *
     * @return string
     */
    public function getConfigValue($path)
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get Media Path
     *
     * @return string
     */
    public function getMediaPath()
    {
        return $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath();
    }
    
    /**
     * check is image or link
     * @param string $imageName
     * @param int $queryId
     * @param int $commandId
     */
    public function isImage($imageName, $order_id)
    {
        $url = $this->getImageMediaPath($imageName, $order_id);
        $imageCheck = !empty($url)?getimagesize($url):"false";
        if (is_array($imageCheck) && $imageCheck!==false) {
            return true;
        } else {
            return false;
        }
    }

     /**
      * get complete imge url
      * @param  string $imageName
      * @return string
      */
    public function getImageMediaPath($imageName, $order_id)
    {
        return $this->mediaDirectory->getAbsolutePath(
            'orderhistorycommunication/communication/'.$order_id.'/'.$imageName
        );
    }
    
    /**
     * Send Mail To Customer
     */
    public function sendMail($orderData)
    {
        $order = $this->order->create()->load($orderData->getOrderId());
        $customer = $this->customer->getById($order->getCustomerId());
        $custName = $customer->getFirstname()." ".$customer->getLastname();
        $senderInfo = [
            'name' => $custName,
            'email' => $customer->getEmail()
        ];
        $receiverInfo = $this->getAdminSenderData();
        $emailTemplateVariables = [];
        $emailTemplateVariables['name'] = __("Admin");
        $emailTemplateVariables['comment'] = $orderData->getComment();
        $template = self::ORDER_COMMUNICATION_EMAIL;
        $this->mailData($emailTemplateVariables, $senderInfo, $receiverInfo, $orderData, $template);
    }
    
    /**
     * Send Mail To Admin
     */
    public function sendAdminMail($orderData)
    {
        $order = $this->order->create()->load($orderData->getOrderId());
        $customer = $this->customer->getById($order->getCustomerId());

        $custName = $customer->getFirstname()." ".$customer->getLastname();
        $receiverInfo = [
            'name' => $custName,
            'email' => $customer->getEmail()
        ];
        $senderInfo = $this->getAdminSenderData();
        $emailTemplateVariables = [];
        $emailTemplateVariables['name'] = $custName;
        $emailTemplateVariables['comment'] = $orderData->getComment();
        $template = self::ORDER_COMMUNICATION_EMAIL_ADMIN;
        $this->mailData($emailTemplateVariables, $senderInfo, $receiverInfo, $orderData, $template);
    }

    /**
     * Send Mail
     */

    public function mailData($emailTemplateVariables, $senderInfo, $receiverInfo, $orderData, $template)
    {
        $this->temp_id = $this->getTemplateId($template);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $imageUploadPath = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath('orderhistorycommunication/communication/'.$orderData->getOrderId().'/');
        if (!empty($orderData->getAttachment())) {
            $imagePath = $imageUploadPath.$orderData->getAttachment();
            $body = $this->file->read($imagePath);
            $mimetype = mime_content_type($imagePath);
            $this->transportBuilder->addAttachment(
                $body,
                $orderData->getAttachment(),
                $mimetype
            );
        }
        try {
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->logger->critical($e->getMessage());
        }
        $this->inlineTranslation->resume();
    }

    /**
     * Get Template Id
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath);
    }

    /**
     * Get Admin Sender Data
     */

    public function getAdminSenderData()
    {
        $adminEmail=$this->scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $senderInfo = ['name'=>__('Admin'),'email'=>$adminEmail];
        return $senderInfo;
    }

    /**
     * Generate Template For Mail
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $template =  $this->transportBuilder->setTemplateIdentifier($this->temp_id)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($senderInfo)
                ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }
}
