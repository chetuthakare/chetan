<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Model;

use Magento\Directory\Model\CurrencyFactory;
use SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplateFactory;

class PersonalizeProductData extends \Magento\Framework\DataObject
{


    public function __construct(
        CurrencyFactory $currencycode,
        \SetuBridge\PersonalizationTemplate\Model\ProductEditpageDataFactory $productEditpageDataFactory,
        PersonalizationTemplateFactory $personalizationTemplateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \SetuBridge\Personalization\Model\SaveCustomerProductFactory $saveCustomerCollectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \SetuBridge\Personalization\Model\PersonalizeProduct $personalizeProduct,
        \SetuBridge\Color\Model\DataFactory $collectionFactory,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->currencycode= $currencycode;
        $this->personalizationTemplateFactory = $personalizationTemplateFactory;
        $this->productEditpageFactory = $productEditpageDataFactory;
        $this->_storeManager = $storeManager;
        $this->localecurrency = $localeCurrency;
        $this->saveCustomerCollectionFactory = $saveCustomerCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->personalizeProduct =$personalizeProduct;
        $this->_collectionFactory = $collectionFactory;
        $this->request = $request;
    }


    public function prepareProductInfo($product,$designId = null){

        return [
            'images'=>$this->getProductJsonData($product,$designId),
            'name' =>$product->getName(),
            'product_type' =>$product->getTypeId(),
            'price' =>$product->getFinalPrice(),
            'price_currency_symbol' =>$this->getStoreCurrency(),
            'colors' =>$this->getPersonalizationColors($product->getPersonalizationColors()),
            'editProduct' => $this->getProductIsEdit(),
            'designerType' => $product->getDesignerType(),
            'url' => $product->getProductUrl()
        ];
    }

    public function getStoreCurrency(){
        $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        return $this->localecurrency->getCurrency($currencycode)->getSymbol();
    }

    private function getProductIsEdit(){
        $itemId = $this->request->getParam("itemId");
        $sessionJsonData = $this->personalizeProduct->getSessionPersonalizationJsonData();
        if($itemId && $sessionJsonData && array_key_exists($itemId,$sessionJsonData)){
            return true;
        }
        else{
            return false;
        }
    }

    private function getProductJsonData($product,$designId){
        $itemId = $this->request->getParam("itemId");

        $sessionJsonData = $this->personalizeProduct->getSessionPersonalizationJsonData();
        if($itemId && $sessionJsonData && array_key_exists($itemId,$sessionJsonData)){

            return $sessionJsonData[$itemId];

        }
        else{

            return $this->getProductTemplateData($product,$designId);

        }
    }

    private function getProductTemplateData($product,$designId){
        $productId = $product->getId();
        if($designId && $this->_customerSession->create()->isLoggedIn()){

            $customerId = $this->_customerSession->create()->getCustomerData()->getId();

            $personalization = $this->saveCustomerCollectionFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_save_id',$designId)
            ->addFieldToFilter('product_id',$productId)
            ->addFieldToFilter('customer_id',$customerId)
            ->getFirstItem();
            if(!empty($personalization->getData())){
                $jsondata = json_decode($personalization->getPersonalizationJsonData());
            }
            else{
                $personalization  = $this->productEditpageFactory->create()->getCollection()->addFieldToFilter('product_id',$productId)->getFirstItem();

                if($personalization->getTemplateId() == $product->getPersonalizationTemplate()){
                    $jsondata = json_decode($personalization->getPersonalizationJsonData());
                }
                else{
                    $personalization = $this->personalizationTemplateFactory->create()->load($product->getPersonalizationTemplate());
                    $jsondata = json_decode($personalization->getPersonalizationJsonData());
                }
            }
        }
        else{
            $personalization  = $this->productEditpageFactory->create()->getCollection()->addFieldToFilter('product_id',$productId)->getFirstItem();

            if($personalization->getTemplateId() == $product->getPersonalizationTemplate()){
                $jsondata = json_decode($personalization->getPersonalizationJsonData());
            }
            else{
                $personalization = $this->personalizationTemplateFactory->create()->load($product->getPersonalizationTemplate());
                $jsondata = json_decode($personalization->getPersonalizationJsonData());
            }
        }

        if($jsondata){
            return $jsondata;
        }
        else{
            return null;
        }


    }

    private function getPersonalizationColors($colors){

        /*if($colors){
            $colorCollection = $this->_collectionFactory->create()->getCollection()
            ->addFieldToSelect(array('color_code','color_name'))->addFieldToFilter('color_code',explode(",", $colors));
            return $colorCollection->getData();
        }*/
        return explode(",", $colors);
    }

}