<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;

class GetSelectedOptions extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Helper\Product\Configuration $productConfig    

    ) 
    {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->_productConfig = $productConfig;
    }

    public function execute()
    {    
        $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);

        try { 
            $itemId = (int)$this->getRequest()->getParam('itemId');

            $quoteItem = $this->getItemById(intval($itemId));

          //  $itemOptions = $this->getProductOptions($quoteItem);

            $jsonFactory->setData(['error'=>false, 'itemQty' => $quoteItem->getQty()]);
        } catch (\Exception $e) {
            $jsonFactory->setData(['error'=>true,'erro_message'=> $e->getMessage()]);
            return $jsonFactory;
        }
        return $jsonFactory;
    }

    private function getItemById($itemId){

        $items = $this->checkoutSession->getQuote()->getAllItems();
        $returnItem=null;

        foreach ($items as $item){
            if ($item->getId() == $itemId) {
                $returnItem = $item;
            }
        }
        return $returnItem;
    }

    public function getProductOptions($item)
    {
        /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
        $helper = $this->_productConfig;
        return $helper->getCustomOptions($item);
    }


} 

