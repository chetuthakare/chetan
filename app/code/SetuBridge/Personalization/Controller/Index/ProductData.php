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
use SetuBridge\Personalization\Model\PersonalizeProductData;

class ProductData extends \Magento\Framework\App\Action\Action
{

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        PersonalizeProductData $personalizeProductData
    ) 
    {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->_personalizeProductData = $personalizeProductData;
    }

    public function execute()
    {    
        $productId = (int)$this->getRequest()->getParam('product');

        $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);

        $response=['error'=>true,'message'=>__('We can\'t find information for given product.')];
        try {
            $product = $this->productRepository->getById($productId);
            if ($product) {
                $productInfo = $this->_personalizeProductData->prepareProductInfo($product);
                $url = $product->getProductUrl();
                $response=['error'=>false,'data'=>['productInfo' => $productInfo,'url' => $url]];
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
        } catch (\Exception $e) {
            $response['message']= $e->getMessage();
        }  

        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $jsonResult->setData($response); 
    }

} 

