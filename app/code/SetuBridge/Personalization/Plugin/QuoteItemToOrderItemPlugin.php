<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Plugin;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Catalog\Model\Product\Type as ProductType;

class QuoteItemToOrderItemPlugin 
{

    protected $serializer;

    public function __construct(
        Json $serializer
    )
    {
        $this->serializer = $serializer;
    }

    public function aroundConvert(ToOrderItem $subject, callable $proceed, $quoteItem, $data)
    {
        $orderItem = $proceed($quoteItem, $data);
        if(!$orderItem->getParentItemId() && $orderItem->getProductType() == ProductType::TYPE_SIMPLE){
            if ($additionalOptionsQuote = $quoteItem->getOptionByCode('additional_options')) {
                $additionalOptionsQuote=$this->serializer->unserialize($additionalOptionsQuote->getValue());
                if($additionalOptionsOrder = $orderItem->getProductOptionByCode('additional_options')){
                    $additionalOptions = array_merge($additionalOptionsQuote, $additionalOptionsOrder);
                }
                else{
                    $additionalOptions = $additionalOptionsQuote;
                }
                if(!empty($additionalOptions)){
                    $options = $orderItem->getProductOptions();
                    $options['additional_options'] = $additionalOptions;
                    $orderItem->setProductOptions($options);
                }
            }
            if($quoteItem->getIsPersonalizationItem() && !$quoteItem->getParentItemId()){
                $orderItem->setIsPersonalizationItem($quoteItem->getIsPersonalizationItem());
            }
        }
        return $orderItem;
    }
}

