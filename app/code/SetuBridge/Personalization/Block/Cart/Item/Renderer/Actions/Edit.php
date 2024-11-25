<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Block\Cart\Item\Renderer\Actions;

class Edit extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Edit
{
    /**
    * Get item configure url
    *
    * @return string
    */
    public function getConfigureUrl()
    {
        if($this->getItem()->getIsPersonalizationItem()){
            return $this->getUrl(
                'personalization/index/editor',
                [
                    'itemId' => $this->getItem()->getId(),
                    'product' => $this->getItem()->getProduct()->getId()
                ]
            );
        }
        else{
            return $this->getUrl(
                'checkout/cart/configure',
                [
                    'id' => $this->getItem()->getId(),
                    'product_id' => $this->getItem()->getProduct()->getId()
                ]
            );
        }
    }
}
