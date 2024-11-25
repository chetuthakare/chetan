<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;

class LoadMyDesign extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SetuBridge\Personalization\Model\SaveCustomerProductFactory $saveCustomerCollectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency

    ) 
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->saveCustomerCollectionFactory = $saveCustomerCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->localecurrency = $localeCurrency;
    }

    public function execute()
    {    
        $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);

        $params = $this->getRequest()->getPostValue('mydesign');
        try { 
            $product = $this->productRepository->getById($params['product_id']);

            if ($product) {
                $response=['error'=>false,'data'=>$this->prepareProductInfo($product,$params)];

                $jsonFactory->setData(['error'=>false,'data'=>$this->prepareProductInfo($product,$params)]);
                return $jsonFactory;

            }
        } catch (\Exception $e) {
            $jsonFactory->setData(['error'=>true,'erro_message'=> $e->getMessage()]);
            return $jsonFactory;
        }
        $jsonFactory->setData(['error'=>true,'erro_message'=>"Error happens. Please try again."]);
        return $jsonFactory;

    }

    public function prepareProductInfo($product,$params){

        return [
            'images'=>$this->getProductTemplateData($product,$params),
            'name' =>$product->getName(),
            'price' =>$product->getFinalPrice(),
            'price_currency_symbol' =>$this->getStoreCurrency(),
            'colors' =>$this->getPersonalizationColors($product->getPersonalizationColors())
        ];
    }


    private function getProductTemplateData($product,$params){
        $productId = $product->getId();
        $designId = $params['customer_save_id'];
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
        }

        if($jsondata){
            return $jsondata;
        }
        else{
            return null;
        }


    }

    public function getStoreCurrency(){

        $currencycode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        return $this->localecurrency->getCurrency($currencycode)->getSymbol();
    }

    private function getPersonalizationColors($colors){
        return explode(",", $colors);

    }
} 

