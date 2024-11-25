<?php

/** Setubridge Technolabs
 * http://www.setubridge.com/
 * @author SetuBridge
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 **/

namespace SetuBridge\Personalization\Controller\Index;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;

class DownloadSourceZip extends \Magento\Framework\App\Action\Action
{

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    protected $quoteFactory;
    protected $zipArchive;
    protected $directoryList;
    protected $driver;
    protected $fileFactory;
    protected $personalizeProduct;
    protected $_helperData;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Shell\Driver $driver,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        OrderRepositoryInterface $orderRepository,
        QuoteFactory $quoteFactory,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \SetuBridge\Personalization\Helper\Data $helperData,
        \Magento\Framework\Archive\Zip $zip
    ) {
        parent::__construct($context);
        $this->zipArchive = $zip;
        $this->directoryList = $directoryList;
        $this->driver = $driver;
        $this->fileFactory = $fileFactory;
        $this->orderRepository = $orderRepository;
        $this->personalizeProduct = $personalizeProduct;
        $this->quoteFactory = $quoteFactory;
        $this->_helperData = $helperData;
    }
    public function execute()
    {

        try {

            $ordercollection = $this->orderRepository->get($this->getRequest()->getParam('orderId'));
            $quoteid = $ordercollection->getQuoteId();
            $quotecollection = $this->quoteFactory->create()->load($quoteid);
            $itemcollection = $quotecollection->getAllItems();
            $enabledOutputFiles = explode(',', $this->_helperData->getEnabledOutputFiles());
            foreach ($itemcollection as $key => $items) {
                $fontfamilies = $items->getData('font_families');
                $itemid = $items->getData('item_id');
                $personalization_color = $items->getData('personalization_color');
                $personalization_json = json_decode($items->getData('personalization_json'), true);
                $arrfont = json_decode($fontfamilies, true);
                $canvasarea_svg_data = json_decode($items->getData('canvasarea_svg_data'), true);
                $full_svg_data = json_decode($items->getData('full_svg_data'), true);
                if (!empty($fontfamilies)) {
                    $arrfont = json_decode($fontfamilies, true);
                    //echo "hello";
                    $this->personalizeProduct->saveCustomerQuoteItemFonts($this->getRequest()->getParam('orderId'), $itemid, $arrfont);
                }
                if ($enabledOutputFiles && !empty($enabledOutputFiles) && in_array("configure_svg", $enabledOutputFiles)) {
                    $this->personalizeProduct->saveCustomerQuoteItemSvgImage($this->getRequest()->getParam('orderId'), $itemid, $canvasarea_svg_data);
                }
                if ($enabledOutputFiles && !empty($enabledOutputFiles) && in_array("full_svg", $enabledOutputFiles)) {
                    $this->personalizeProduct->saveCustomerQuoteItemFullSvgImage($this->getRequest()->getParam('orderId'), $itemid, $full_svg_data);
                }
                if (class_exists('TCPDF')) {
                    if ($enabledOutputFiles && !empty($enabledOutputFiles) && in_array("configure_pdf", $enabledOutputFiles)) {
                        $this->personalizeProduct->saveCustomerQuoteItemPDF($this->getRequest()->getParam('orderId'), $itemid);
                    }
                    if ($enabledOutputFiles && !empty($enabledOutputFiles) && in_array("full_pdf", $enabledOutputFiles)) {
                        if ($personalization_color) {
                            $this->personalizeProduct->saveCustomerQuoteItemFullImagesPDF($this->getRequest()->getParam('orderId'), $itemid, $personalization_json, $personalization_color);
                        } else {
                            $this->personalizeProduct->saveCustomerQuoteItemFullImagesPDF($this->getRequest()->getParam('orderId'), $itemid, $personalization_json, false);
                        }
                    }
                }
            }


            if (!class_exists('\ZipArchive')) {
                $this->messageManager->addError(__('ZipArchive class not found.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setRefererOrBaseUrl();
                return $resultRedirect;
            }
            $orderId = $this->getRequest()->getParam('orderId');
            $orderIncreamentId = $this->getRequest()->getParam('orderIncreamentId');
            $dir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $rootPath = $dir . '/Personalization/SourceCode/Order/' . $orderId;

            if ($this->isFileExist($rootPath)) {
                chdir($rootPath);
                $zip = new \ZipArchive();
                $zip->open($orderId . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($rootPath),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($rootPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }

                $zip->close();

                $zipname = $orderId;
                if ($orderIncreamentId) {
                    $zipname = $orderIncreamentId;
                }
                $this->fileFactory->create(
                    $zipname . '.zip',
                    [
                        'type' => 'filename',
                        'value' =>  $rootPath . '/' . $orderId . '.zip',
                        'rm' => true
                    ],
                    \Magento\Framework\App\Filesystem\DirectoryList::ROOT,
                    'application/zip'
                );
            } else {
                $this->messageManager->addError(__('Some issues face in images to download.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setRefererOrBaseUrl();
                return $resultRedirect;
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Attention: Something went wrong."));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }
    }

    private function isFileExist($rootPath)
    {
        return file_exists($rootPath);
    }
}
