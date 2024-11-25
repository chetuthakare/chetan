<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Captcha\Observer\CaptchaStringResolver;
use Magento\Framework\App\Filesystem\DirectoryList;

class OrderObserver implements ObserverInterface
{
    protected $_fileSystem;
    protected $orderRepository;
    const ORDER_IMAGE_DIR_PATH='Personalization/SourceCode/Order/%d';
    const QUOTE_IMAGE_DIR_PATH='Personalization/SourceCode/Quote/%d';

    public $messageManager;
    public $personalizationOrderData;
    public $filesystem;
    public $file;
    public $_countryFactory;
    public $personalizeProduct;
    public $mediaDirectory;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        DirectoryList $directoryList,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Filesystem\Io\File $file,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \SetuBridge\Personalization\Model\PersonalizationOrderData $personalizationOrderData
    ) {
        $this->filesystem = $filesystem;
        $this->orderRepository = $orderRepository;
        $this->file = $file;
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->messageManager = $messageManager;
        $this->_countryFactory = $countryFactory;
        $this->personalizeProduct =$personalizeProduct;
        $this->personalizationOrderData = $personalizationOrderData;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        try {
            $order_ids  = $observer->getEvent()->getOrderIds();
            $order_id   = $order_ids[0];

            $order = $this->orderRepository->get($order_id);

            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

            $quotePath = $mediaDirectory->getAbsolutePath($this->getQuoteDir($order->getQuoteId()));
            if($this->isFileExist($quotePath)){


                $orderPath = $mediaDirectory->getAbsolutePath($this->getOrderDir($order_id));


                $this->file->checkAndCreateFolder($orderPath);

                $status = $this->writeCustomerInfoFile($order,$orderPath);

                if (is_dir($quotePath) && !$this->personalizeProduct->dir_is_empty($quotePath)) {
                    $this->recurse_copy(
                        $quotePath,$orderPath
                    );

                    $this->personalizeProduct->removeDirectory($quotePath);
                }elseif(is_dir($quotePath) && $this->personalizeProduct->dir_is_empty($quotePath)){
                    $this->personalizeProduct->removeDirectory($quotePath);
                }

                $AllItemsIds = [];
                foreach($order->getAllVisibleItems() as $item){

                    if (!$item->isDeleted() && !$item->getParentItemId() && ($item->getProductType() == "simple" || $item->getProductType() == "configurable")) {

                        $AllItemsIds[] = $item->getData('quote_item_id');

                        if($item->getIsPersonalizationItem()){
                            $itemId = $item->getId();

                            $quoteItemId = $item->getQuoteItemId();
                            $sessionJsonData = $this->personalizeProduct->getSessionPersonalizationJsonData();

                            if($itemId && $sessionJsonData && array_key_exists($item->getData('quote_item_id'),$sessionJsonData)){

                                $personalizationJsonObject = $sessionJsonData[$item->getData('quote_item_id')];

                                $this->personalizationOrderData->load($itemId,'order_item_id');

                                if($this->personalizationOrderData->getEntityId()){
                                    $this->personalizationOrderData->setData('personalization_json_data',$personalizationJsonObject);
                                    $this->personalizationOrderData->setData('order_id',$order_id);
                                    $this->personalizationOrderData->setData('quote_item_id',$quoteItemId);
                                }
                                else{
                                    $this->personalizationOrderData->setData('personalization_json_data',$personalizationJsonObject);
                                    $this->personalizationOrderData->setData('order_id',$order_id);
                                    $this->personalizationOrderData->setData('order_item_id',$itemId);
                                    $this->personalizationOrderData->setData('quote_item_id',$quoteItemId);
                                }

                                $this->personalizationOrderData->save();
                                $this->personalizationOrderData->unsetData();

                            }
                        }
                    }

                }

                if($this->personalizeProduct->getSessionPersonalizationJsonData()){
                    $this->personalizeProduct->unsSessionPersonalizationJsonData();
                }
                $this->itemIsExistInFolder($orderPath, $AllItemsIds);

            }


        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    private function isFileExist($quotePath){
        return file_exists($quotePath);
    }

    public function getOrderDir($id){
        return sprintf(self::ORDER_IMAGE_DIR_PATH,$id);
    }
    public function getQuoteDir($id){
        return sprintf(self::QUOTE_IMAGE_DIR_PATH,$id);
    }

    public function itemIsExistInFolder($orderPath, $AllItemsIds)
    {
        $scanned_directory = array_diff(scandir($orderPath), array('..', '.','customer-info.txt'));

        foreach($scanned_directory as $dir){
            if (!in_array($dir, $AllItemsIds)) {
                $this->personalizeProduct->removeDirectory($orderPath.'/'.$dir);
            }
        }
    }

    public function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function writeCustomerInfoFile($order,$orderPath){
        error_reporting(E_ALL);

        $shippinAddress = $order->getShippingAddress();
        $pagename = 'customer-info';

        $newFileName = $orderPath.'/'.$pagename.".txt";

        $customerName = null;
        $customerEmail = null;
        $customerTelephone = null;
        $customerStreet = null;
        $customerCity = null;
        $customerRegion = null;
        $customerCountryId = null;

        if($order->getCustomerName()){
            $customerName = $order->getCustomerName();
        }
        if($order->getCustomerEmail()){
            $customerEmail =$order->getCustomerEmail();
        }
        if($shippinAddress->getTelephone()){
            $customerTelephone = $shippinAddress->getTelephone();
        }

        if($shippinAddress->getStreet()){
            $street = $shippinAddress->getStreet();
            $customerStreet = is_array($street) ? implode(", ", $street) : $street;
        }

        if($shippinAddress->getCity()){
            $customerCity = $shippinAddress->getCity();
        }
        if($shippinAddress->getRegion()){
            $customerRegion = $shippinAddress->getRegion();
        }
        if($shippinAddress->getCountryId()){
            $country = $this->_countryFactory->create()->loadByCode($shippinAddress->getCountryId());
            $customerCountry = $country->getName();
        }

        $newFileContent = 'Customer Name : '.$customerName.PHP_EOL.'Customer Email : '.$customerEmail.PHP_EOL.'Customer Contact Number : '.$customerTelephone.PHP_EOL.'Customer Shpping Address : '.$customerStreet.', '.$customerCity.', '.$customerRegion.', '.$customerCountry;

        if (file_put_contents($newFileName, $newFileContent) !== false) {
            return true;
        } else {
            echo false;
        }
    }

}
