<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DataObject;

class PersonalizationItemData implements ObserverInterface
{

    const QUOTE_DIR_PATH='Personalization/SourceCode/Quote/%d';

    const ORDER_DIR_PATH='Personalization/SourceCode/Order/%d';

    protected $_request;
    public $messageManager;
    public $serializer;
    public $personalizationOrderData;
    public $filesystem;
    public $file;
    public $_registry;
    public $personalizeProduct;
    public $_helperData;
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $registry,
        RequestInterface $request,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \SetuBridge\Personalization\Model\PersonalizationOrderData $personalizationOrderData,
        \SetuBridge\Personalization\Helper\Data $helperData
    )
    {
        $this->filesystem = $filesystem;
        $this->personalizeProduct = $personalizeProduct;
        $this->file = $file;
        $this->messageManager = $messageManager;
        $this->_registry = $registry;
        $this->_request = $request;
        $this->personalizationOrderData = $personalizationOrderData;
        $this->serializer =$serializer;
        $this->_helperData =$helperData;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        try {
            $quoteItem = $observer->getEvent()->getItem();

            if(!$quoteItem->getParentItemId() && ($quoteItem->getProductType() == "simple" || $quoteItem->getProductType() == "configurable"))
            {
                $personalizationItemId = $quoteItem->getPersonalizationItemId();
                $itemId = $quoteItem->getId();

                $personalizationCartData = $this->personalizeProduct->getPersonalizationCartData();

                if($itemId != $personalizationItemId && $quoteItem->getIsPersonalizationItem()){


                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

                    $oldQuoteId = $this->getPersonalizationOldQuoteId();

                    $reorderOrderId = $quoteItem->getSessionPersonalizationReorderOrderId();
                    $reorderOrderItemId = $quoteItem->getSessionPersonalizationReorderItemId();

                    if($reorderOrderItemId && $reorderOrderId){

                        $orderPath = $mediaDirectory->getAbsolutePath($this->getOrderDir($reorderOrderId));

                        $newQuotePath = $mediaDirectory->getAbsolutePath($this->getQuoteDir($quoteItem->getQuote()->getId()));

                        $this->file->checkAndCreateFolder($newQuotePath);
                        if(file_exists($orderPath)){

                            $personalizationOrder = $this->personalizationOrderData->load($reorderOrderItemId,'order_item_id');

                            if($personalizationOrder && $personalizationOrder->getOrderId() == $reorderOrderId){

                                $this->personalizeProduct->setSessionPersonalizationJsonData($itemId, $personalizationOrder->getPersonalizationJsonData(), true);

                                $itemImagesPath = $orderPath.'/'.$personalizationOrder->getQuoteItemId();

                                if(file_exists($itemImagesPath)){

                                    $newCreatedItemFolder = $newQuotePath.'/'.$itemId;

                                    $this->file->checkAndCreateFolder($newCreatedItemFolder);

                                    if(file_exists($newCreatedItemFolder)){

                                        $this->personalizeProduct->copyFolderSourceToDestination($itemImagesPath, $newCreatedItemFolder, false);

                                        $quoteItem->setPersonalizationItemId($itemId);
                                        $personalizationjson=json_encode($this->personalizeProduct->getSessionPersonalizationJsonData());
                                    $quoteItem->setPersonalizationJson($personalizationjson);
                                        $quoteItem->save();
                                    }
                                }

                            }

                        }
                    }
                    else if($oldQuoteId){

                        $quotePath = $mediaDirectory->getAbsolutePath($this->getQuoteDir($oldQuoteId));

                        $newQuotePath = $mediaDirectory->getAbsolutePath($this->getQuoteDir($quoteItem->getQuote()->getId()));

                        if(file_exists($quotePath)){

                            $itemImagesPath = $quotePath.'/'.$personalizationItemId;

                            if(file_exists($itemImagesPath)){

                                $newCreatedItemFolder = $newQuotePath.'/'.$itemId;

                                $this->file->checkAndCreateFolder($newCreatedItemFolder);

                                if(file_exists($newCreatedItemFolder)){

                                    $this->personalizeProduct->copyFolderSourceToDestination($itemImagesPath, $newCreatedItemFolder, true);

                                    $quoteItem->setPersonalizationItemId($itemId);
                                    $personalizationjson=json_encode($this->personalizeProduct->getSessionPersonalizationJsonData());
                                    $quoteItem->setPersonalizationJson($personalizationjson);
                                    $quoteItem->save();
                                }
                            }


                        }
                    }

                }

                if($personalizationCartData){

                    $itemId = $quoteItem->getId();

                    $notCreatedDataProId = $this->personalizeProduct->getPersonalizationSourceDataSavedId();

                    if(!$notCreatedDataProId || $notCreatedDataProId != $itemId){

                        $enabledOutputFiles = explode(',', $this->_helperData->getEnabledOutputFiles());

                        $this->personalizeProduct->setPersonalizationSourceDataSavedId($itemId);

                        if(!$quoteItem->getIsPersonalizationItem()){
                            $quoteItem->setIsPersonalizationItem(true);
                            $quoteItem->setPersonalizationItemId($itemId);
                            $quoteItem->save();
                        }


                        $option = $quoteItem->getOptionByCode('info_buyRequest');
                        $data = $option ? $this->serializer->unserialize($option->getValue()) : [];

                        $buyRequest = new \Magento\Framework\DataObject($data);
                        $getPersonalizationProductColor = $buyRequest->getPersonalizationProductColor();

                        $params = $personalizationCartData->getParams();

                        if (is_array($params) && array_key_exists("personalize_image",$params))
                        {

                            if($getPersonalizationProductColor){
                                $personalizationData = $this->getPersonlizeArrayByColor($params["personalize_image"], $getPersonalizationProductColor);
                            }
                            else{
                                $personalizationData = $this->getPersonlizeArrayByColor($params["personalize_image"]);
                            }

                            if(!empty($personalizationData)){

                                if($getPersonalizationProductColor){
                                    $this->personalizeProduct->setSessionPersonalizationJsonData($itemId,$personalizationCartData, false, $getPersonalizationProductColor);
                                    $personalizationjson=json_encode($this->personalizeProduct->getSessionPersonalizationJsonData());
                                    $quoteItem->setPersonalizationJson($personalizationjson);
                                    $quoteItem->setPersonalizationColor($getPersonalizationProductColor);
                                    $quoteItem->save();
                                }
                                else{
                                    $this->personalizeProduct->setSessionPersonalizationJsonData($itemId,$personalizationCartData);
                                    $personalizationjson=json_encode($this->personalizeProduct->getSessionPersonalizationJsonData());
                                    $quoteItem->setPersonalizationJson($personalizationjson);
                                    $quoteItem->setPersonalizationColor($getPersonalizationProductColor);
                                    $quoteItem->save();
                                }

                                $QuoteId = $quoteItem->getQuoteId();

                                $canvasAreaSvgData=json_encode($personalizationCartData->getParam('canvasAreaSvgData'));
                                $fullImageSvgData=json_encode($personalizationCartData->getParam('fullImageSvgData'));
                                $fontFamilies=json_encode($personalizationCartData->getParam('fontFamilies'));

                                    $quoteItem->setCanvasareaSvgData($canvasAreaSvgData);
                                    $quoteItem->setFullSvgData($fullImageSvgData);
                                    $quoteItem->setFontFamilies($fontFamilies);
                                    $quoteItem->save();

                               $this->personalizeProduct->saveCustomerItemImage($QuoteId,$itemId,$personalizationData);

                               $this->personalizeProduct->saveCustomerQuoteItemImage($QuoteId,$itemId,$personalizationCartData);
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->messageManager->addErrorMessage("Attention: Something went wrong.");
        }
    }

    public function getPersonalizationOldQuoteId()
    {
        return $this->_registry->registry('personalization_old_quote_id');
    }

    private function getQuoteDir($id){
        return sprintf(self::QUOTE_DIR_PATH,$id);
    }

    private function getOrderDir($id){
        return sprintf(self::ORDER_DIR_PATH,$id);
    }

    private function getPersonlizeArrayByColor($options, $color = null){

        $optionslength = count($options);
        if($optionslength > 1 && $color){
            $returnArray = [];
            foreach($options as $key => $option){
                if($key == $color){
                    $returnArray = $option;
                    break;
                }
            }
            return $returnArray;
        }
        else{
            $returnArray = [];
            foreach($options as $key => $option){
                if($option){
                    $returnArray = $option;
                    break;
                }
            }
            return $returnArray;
        }
    }
}
