<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class SalesQuoteMergeData implements ObserverInterface
{

    const QUOTE_DIR_PATH='Personalization/SourceCode/Quote/%d';
    protected $_request;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        RequestInterface $request,
        \Magento\Framework\Registry $registry
    ) {
        $this->filesystem = $filesystem;
        $this->_request = $request;
        $this->directoryList =$directoryList;
        $this->personalizeProduct =$personalizeProduct;
        $this->file = $file;
        $this->messageManager = $messageManager;
        $this->_registry = $registry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        try {
            $newQuote = $observer->getQuote();
            $oldQuote = $observer->getSource();

            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

            $quotePath = $mediaDirectory->getAbsolutePath($this->getQuoteDir($oldQuote->getId()));


            if(file_exists($quotePath)){

                $this->setPersonalizationOldQuoteId($oldQuote->getId());

                $newQuotePath = $mediaDirectory->getAbsolutePath($this->getQuoteDir($newQuote->getId()));

                $this->file->checkAndCreateFolder($newQuotePath);

                foreach ($newQuote->getAllVisibleItems() as $item) {

                    if($item){
                        $itemImagesPath = $quotePath.'/'.$item->getPersonalizationItemId();

                        if($item->getIsPersonalizationItem() && $item->getPersonalizationItemId() && file_exists($itemImagesPath)){


                            $newCreatedItemFolder = $newQuotePath.'/'.$item->getItemId();

                            $this->file->checkAndCreateFolder($newCreatedItemFolder);


                            if(file_exists($newCreatedItemFolder)){
                                $this->personalizeProduct->copyFolderSourceToDestination($newCreatedItemFolder, $itemImagesPath);
                            }

                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage("Attention: Something went wrong.");
        }

    }

    public function setPersonalizationOldQuoteId($id)
    {
        $this->_registry->register('personalization_old_quote_id', $id);
    }

    private function getQuoteDir($id){
        return sprintf(self::QUOTE_DIR_PATH,$id);
    }

}