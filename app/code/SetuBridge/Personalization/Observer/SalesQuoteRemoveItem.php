<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class SalesQuoteRemoveItem implements ObserverInterface
{

    const QUOTE_DIR_PATH='Personalization/SourceCode/Quote/%d/%d';
    protected $_request;

    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        RequestInterface $request
    ) {
        $this->_request = $request;
        $this->directoryList =$directoryList;
        $this->personalizeProduct =$personalizeProduct;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $params = $this->_request->getParams();
        if(array_key_exists('item_id',$params)){
            $itemId = $params['item_id'];
        }
        elseif(array_key_exists('id',$params)){
            $itemId = $params['id'];
        }

        $quoteId = $observer->getQuoteItem()->getQuote()->getId();

        if(isset($itemId) && $itemId){

            $directory = $this->getRemoveQuoteDir($quoteId,$itemId);


            $directoryPath = $this->directoryList->getPath('media').'/'.$directory;
            if (is_dir($directoryPath)) {

                $this->personalizeProduct->removeDirectory($directoryPath);
            }
        }
    }
    public function getRemoveQuoteDir($QuoteId,$itemId){
        return sprintf(self::QUOTE_DIR_PATH,$QuoteId,$itemId);
    }
}