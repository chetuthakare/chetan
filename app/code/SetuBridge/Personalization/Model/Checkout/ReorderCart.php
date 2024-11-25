<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Model\Checkout;

class ReorderCart extends \Magento\Checkout\Model\Cart
{


    /**
    * Convert order item to quote item
    *
    * @param \Magento\Sales\Model\Order\Item $orderItem
    * @param true|null $qtyFlag if is null set product qty like in order
    * @return $this
    */
    public function addOrderItem($orderItem, $qtyFlag = null)
    {
        /* @var $orderItem \Magento\Sales\Model\Order\Item */

        if ($orderItem->getParentItem() === null) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                /**
                * We need to reload product in this place, because products
                * with the same id may have different sets of order attributes.
                */
                $product = $this->productRepository->getById($orderItem->getProductId(), false, $storeId, true);
            } catch (NoSuchEntityException $e) {
                return $this;
            }
            $info = $orderItem->getProductOptionByCode('info_buyRequest');
            $info = new \Magento\Framework\DataObject($info);
            if ($qtyFlag === null) {
                $info->setQty($orderItem->getQtyOrdered());
            } else {
                $info->setQty(1);
            }

            if($orderItem->getIsPersonalizationItem()){
                $info->setSalesOrderId($orderItem->getOrderId());
                $info->setSalesOrderItemId($orderItem->getId());
            }
            
            $this->addProduct($product, $info);
        }
        return $this;
    }
}