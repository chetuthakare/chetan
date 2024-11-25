<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Plugin\Quote\Item;

use Magento\Quote\Model\Quote\Item\Compare as QuoteItemCompare;

class Compare
{

    public function afterCompare(QuoteItemCompare $subject, $result, $target, $compared)
    {

        if($target->getIsPersonalizationItem()){

            return false;

        }

        return $result;
    }

}