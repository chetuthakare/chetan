<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Plugin\Quote;

use Magento\Quote\Model\Quote\Item as QuoteItem;

class Item
{

    public function afterRepresentProduct(QuoteItem $subject, $result, $product)
    {

        if($product->getIsPersonalizationProduct() || $subject->getIsPersonalizationItem()){
            return false;
        }

        return $result;
    } 
}