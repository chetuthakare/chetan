<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Model\CustomerData;

/**
* Default item
*/
class DefaultItem extends \Magento\Checkout\CustomerData\DefaultItem
{

    protected function getConfigureUrl()
    {
        if($this->item->getIsPersonalizationItem()){
            return $this->urlBuilder->getUrl(
                'personalization/index/editor',
                [
                    'itemId' => $this->item->getId(),
                    'product' => $this->item->getProduct()->getId()
                ]
            );
        }
        else{
            return $this->urlBuilder->getUrl(
                'checkout/cart/configure',
                ['id' => $this->item->getId(), 'product_id' => $this->item->getProduct()->getId()]
            );
        }
    }
}
