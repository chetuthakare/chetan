<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\Personalization\Helper;

use Magento\Framework\App\Helper\Context;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $storeManager;
    public $_storeManager;
    public $imageProcessor;
    public $productEditpageFactory;
    public $_backendUrl;
    public $directoryList;
    public $_productRepository;
    public $itemCollection;
    public $orderItemCollectionFactory;
    public $_currentStore;
    public $request;
    public $_configWriter;
    public $resourceConnection;
    public $cacheManager;
    public $_logo;
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SetuBridge\PersonalizationTemplate\Model\ProductEditpageDataFactory $productEditpageDataFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $itemCollection,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \SetuBridge\Personalization\Model\ImageProcessor $imageProcessor,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Theme\Block\Html\Header\Logo $logo
    ) {

        $this->_storeManager = $storeManager;
        $this->imageProcessor = $imageProcessor;
        $this->productEditpageFactory = $productEditpageDataFactory;
        $this->_backendUrl = $backendUrl;
        $this->directoryList = $directoryList;
        $this->_productRepository = $productRepository;
        $this->itemCollection = $itemCollection;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->_currentStore = $this->_storeManager->getStore();
        $this->request = $request;
        $this->_configWriter = $configWriter;
        $this->resourceConnection = $resourceConnection;
        $this->cacheManager = $cacheManager;
        $this->_logo = $logo;
        parent::__construct($context);
    }
    public function getParams()
    {
        return $this->request->getParams();
    }
    public function getItemOptionImages($itemId)
    {

        if ($this->request->getFullActionName() == 'checkout_cart_index') {
            $collection = $this->itemCollection->create();
            $collection->addFieldToFilter(
                \Magento\Quote\Api\Data\CartItemInterface::KEY_ITEM_ID,
                $itemId
            );
            $item = $collection->getFirstItem();
            $quoteId = $item->getQuoteId();
        } else {
            $params = $this->request->getParams();
            if (array_key_exists("order_id", $params) && $this->request->getParam('order_id')) {
                $quoteId = $this->request->getParam('order_id');
            } else {
                $collection = $this->orderItemCollectionFactory->create();
                $collection->addFieldToFilter(
                    'quote_item_id',
                    $itemId
                );
                $item = $collection->getFirstItem();
                $quoteId = $item->getOrderId();
            }
        }
        return $this->imageProcessor->getItemOptionImages($itemId, $quoteId);
    }
    public function getCustomDesignImages($designId)
    {
        return $this->imageProcessor->getCustomDesignImages($designId);
    }
    public function getStore()
    {
        return $this->_currentStore;
    }
    public function getMyDesignUrl()
    {
        return $this->_backendUrl->getUrl('personalization/grid/index', ['_current' => true]);
    }
    public function getPrieviewImgHtml($itemId)
    {
        $previewImage = $this->getItemOptionImage($itemId);
        if ($previewImage) {
            return '<img class="personalization-preview-img" width="100px" id="preview-img-' . $itemId . '"  alt=' . __('Preview') . ' src="' . $previewImage . '" >';
        }
        return '';
    }

    public function getDownloadImages($itemId)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . 'Personalization/personalization/' . $itemId . '.png';
    }

    public function getAdminItemOptionImages($itemId)
    {

        return $this->imageProcessor->getAdminItemOptionImages($itemId);
    }

    public function getPersonalizeConfigValues($code, $storeId = null)
    {
        return $this->scopeConfig->getValue($code, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getConfigValues($code, $storeId = null)
    {
        return $this->scopeConfig->getValue('personalization/general/' . $code, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getButtonConfigValues($code, $storeId = null)
    {
        return $this->scopeConfig->getValue('personalization/button/' . $code, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getLockCustomizationConfigValues($code, $storeId = null)
    {
        return $this->scopeConfig->getValue('personalization/lock_customization/' . $code, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getThemeConfigValues($code, $storeId = null)
    {
        return $this->scopeConfig->getValue('personalization/theme/' . $code, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getDefaultConfigValues($groupId, $code, $storeId = null)
    {
        return $this->scopeConfig->getValue('personalization/' . $groupId . '/' . $code, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getStatus()
    {
        return $this->getConfigValues('active');
    }

    public function getClipartActive()
    {
        return $this->getConfigValues('clipart_active');
    }

    public function getColorActive()
    {
        return $this->getConfigValues('color_active');
    }

    public function getPatternActive()
    {
        return $this->getConfigValues('pattern_active');
    }

    public function getTemplateDesignActive()
    {
        return $this->getConfigValues('templatedesign_active');
    }

    public function getQuoteActive()
    {
        if ($this->getPlainTextActive())
            return $this->getConfigValues('quote_active');
        return 0;
    }

    public function getPlainTextActive()
    {
        return $this->getConfigValues('plainText_active');
    }

    public function getFontActive()
    {
        return $this->getConfigValues('font_active');
    }

    public function getUploadImageActive()
    {
        return $this->getConfigValues('uploadImage_active');
    }

    public function getRulerActive()
    {
        return $this->getConfigValues('ruler_active');
    }

    public function getPricePerArt()
    {
        return $this->getConfigValues('price_art');
    }

    public function getPricePerText()
    {
        return $this->getConfigValues('price_text');
    }

    public function getPopupEnable()
    {
        return $this->getButtonConfigValues('popup_enable');
    }

    public function getButtonText()
    {
        return $this->getButtonConfigValues('button_text');
    }

    public function getButtonListing()
    {
        return $this->getButtonConfigValues('button_listing');
    }

    public function getPricePerPattern()
    {
        return $this->getConfigValues('price_pattern');
    }

    public function getPricePerTemplateDesign()
    {
        return $this->getConfigValues('price_templatedesign');
    }

    public function getPricePerUploadImage()
    {
        return $this->getConfigValues('price_uploadImage');
    }

    public function getThemeLogo()
    {
        return $this->getThemeConfigValues('theme_logo');
    }

    public function getCanvasObjectsCenterAlignmentColor()
    {
        return $this->getThemeConfigValues('canvas_objects_center_alignment_color');
    }

    public function getLockCustomizationTextStatus()
    {
        return $this->getLockCustomizationConfigValues('lock_customization_text');
    }

    public function getLockCustomizationClipartStatus()
    {
        return $this->getLockCustomizationConfigValues('lock_customization_clipart');
    }

    public function getEnabledOutputFiles()
    {
        return $this->getDefaultConfigValues('output_files_setings', 'enabled_output_files');
    }

    public function isOrderSourceFileExist($orderId)
    {
        $mediaUrl = $this->directoryList->getPath('media');
        $orderPath = $mediaUrl . '/Personalization/SourceCode/Order/' . $orderId;
        return file_exists($orderPath);
    }

    public function checkedCustomization($product)
    {
        if ($product) {
            $templateid = $product->getPersonalizationTemplate();

            if ($templateid == 0) {
                $personalization  = $this->productEditpageFactory->create()->getCollection()->addFieldToFilter('product_id', $product->getId())->getFirstItem();
                // $jsondata = array();
                if ($personalization->getTemplateId() == $product->getPersonalizationTemplate() && $personalization->getPersonalizationJsonData()) {
                    $jsondata = json_decode($personalization->getPersonalizationJsonData());
                }

                if (isset($jsondata) && $jsondata) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function deleteOldSampleData($tableName)
    {
        $connection  = $this->resourceConnection->getConnection();
        $tableName   = $this->resourceConnection->getTableName($tableName);
        $sql    = "DELETE FROM " . $tableName . " WHERE sample_data_status = 1";
        $status = $connection->query($sql);
        return $status;
    }

    public function clearCache()
    {
        $this->cacheManager->clean($this->cacheManager->getAvailableTypes());
    }

    public function generalConfiguration()
    {
        if ($this->getThemeLogo()) {
            $logo = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'Personalization/Logo/' . $this->getThemeLogo();
        } else {
            $logo = $this->_logo->getLogoSrc();
        }
        return ['clipart_active' => $this->getClipartActive(), 'color_active' => $this->getColorActive(), 'pattern_active' => $this->getPatternActive(), 'templatedesign_active' => $this->getTemplateDesignActive(), 'plainText_active' => $this->getPlainTextActive(), 'font_active' => $this->getFontActive(), 'uploadImage_active' => $this->getUploadImageActive(), 'price_art' => $this->getPricePerArt(), 'price_text' => $this->getPricePerText(), 'price_pattern' => $this->getPricePerPattern(), 'price_templatedesign' => $this->getPricePerTemplateDesign(), 'price_uploadImage' => $this->getPricePerUploadImage(), 'lock_customization_text' => $this->getLockCustomizationTextStatus(), 'lock_customization_clipart' => $this->getLockCustomizationClipartStatus(), 'theme_logo' => $logo, 'canvas_objects_center_alignment_color' => $this->getCanvasObjectsCenterAlignmentColor(), 'enabled_output_files' => $this->getEnabledOutputFiles(), 'popup_enable' => $this->getPopupEnable(), 'support_multi_addtocart' => $this->getPersonalizeConfigValues('personalization/studio_feature/support_multi_addtocart'), 'status_config_child_template' => $this->getPersonalizeConfigValues('personalization/studio_feature/status_config_child_template'), 'showOutOfAreaAertMessage' => $this->getPersonalizeConfigValues('personalization/studio_feature/status_display_alert_outofarea_object'), 'status_display_help_tab' => $this->getPersonalizeConfigValues('personalization/studio_feature/status_display_help_tab'), 'status_display_help_option' => $this->getPersonalizeConfigValues('personalization/studio_feature/status_display_help_option'), 'help_tab_link_url' => $this->getPersonalizeConfigValues('personalization/studio_feature/help_tab_link_url'), 'help_tab_video_url' => $this->getPersonalizeConfigValues('personalization/studio_feature/help_tab_video_url'), 'status_display_help_link_new_tab' => $this->getPersonalizeConfigValues('personalization/studio_feature/status_display_help_link_new_tab')];
    }
}
