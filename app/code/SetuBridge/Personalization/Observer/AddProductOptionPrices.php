<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;

class AddProductOptionPrices implements ObserverInterface
{
    protected $personalizeProduct;
    protected $sessionFactory;
    protected $serializer;

    public function __construct(
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct
    )
    {
        $this->personalizeProduct = $personalizeProduct;
    }
    public function execute(\Magento\Framework\Event\Observer $observer) {

        $quoteItem = $observer->getEvent()->getData('quote_item');
        $item = $quoteItem->getParentItem() ? $quoteItem->getParentItem() : $quoteItem ;   
        if($this->personalizeProduct->getOptionsPrice() > 0)
        {
            $newPrice=$this->personalizeProduct->getOptionsPrice() + $quoteItem->getProduct()->getFinalPrice();
            $quoteItem->setCustomPrice($newPrice);
            $quoteItem->setOriginalCustomPrice($newPrice);
            $quoteItem->getProduct()->setIsSuperMode(true);
        } 

        if($this->personalizeProduct->getSessionPersonalizationReorderItemId()){
            $quoteItem->setSessionPersonalizationReorderItemId($this->personalizeProduct->getSessionPersonalizationReorderItemId());
            $quoteItem->setSessionPersonalizationReorderOrderId($this->personalizeProduct->getSessionPersonalizationReorderOrderId());
            $quoteItem->setIsPersonalizationItem(true);
            $quoteItem->setPersonalizationItemId($this->personalizeProduct->getSessionPersonalizationReorderItemId());
        }
    }
}

