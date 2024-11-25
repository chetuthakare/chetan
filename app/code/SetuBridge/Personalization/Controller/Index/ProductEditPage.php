<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ProductRepository;

class ProductEditPage extends \Magento\Framework\App\Action\Action
{
    protected $productRepository;
    protected $personalizationFactory;
    protected $productEditpageFactory;
    public function __construct(
        Context $context,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplateFactory $personalizationFactory,
        ProductRepository $productRepository,
        \SetuBridge\PersonalizationTemplate\Model\ProductEditpageDataFactory $productEditpageDataFactory
    )
    {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->personalizationFactory = $personalizationFactory;
        $this->productEditpageFactory = $productEditpageDataFactory;
    }

    public function execute()
    {
        try {
            $templateId = (int)$this->getRequest()->getParam('templateID');
            $productId = (int)$this->getRequest()->getParam('productID');
            $uniqId = $this->getRequest()->getParam('uniqId');
            $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);
            if($templateId && $templateId != 0){
                if($uniqId){

                    $personalization  = $this->productEditpageFactory->create()->getCollection()->addFieldToFilter('uniqid',$uniqId)->getFirstItem();

                    if($personalization->getTemplateId() == $templateId && $personalization->getTemplateId() != null){
                        $jsondata = json_decode($personalization->getPersonalizationJsonData());
                    }
                    else{
                        $personalization = $this->personalizationFactory->create()->load($templateId);
                        $jsondata = json_decode($personalization->getPersonalizationJsonData());
                    }
                }
                else{
                    if($productId){

                        $personalization  = $this->productEditpageFactory->create()->getCollection()->addFieldToFilter('product_id',$productId)->getFirstItem();

                        if($personalization->getTemplateId() == $templateId){

                            $jsondata = json_decode($personalization->getPersonalizationJsonData());
                        }
                        else{
                            $personalization = $this->personalizationFactory->create()->load($templateId);
                            $jsondata = json_decode($personalization->getPersonalizationJsonData());
                        }

                    }
                    else{
                        $personalization = $this->personalizationFactory->create()->load($templateId);
                        $jsondata = json_decode($personalization->getPersonalizationJsonData());
                    }
                }
            }
            else{

                if($productId){
                    $personalization  = $this->productEditpageFactory->create()->getCollection()->addFieldToFilter('product_id',$productId)->getFirstItem();


                    if($personalization->getTemplateId() != null && $personalization->getTemplateId() == 0 && !empty($personalization->getData()) && $personalization->getPersonalizationJsonData() && $personalization->getPersonalizationJsonData() != 'null'){
                        $jsondata = json_decode($personalization->getPersonalizationJsonData());
                    }
                    else{
                        $jsonFactory->setData(['template_data' => null,'error'=>false]);
                        return $jsonFactory;
                    }
                }
                else{
                    $personalization  = $this->productEditpageFactory->create()->getCollection()->addFieldToFilter('uniqid',$uniqId)->getFirstItem();

                    if($personalization->getTemplateId() == 0 && !empty($personalization->getData())){
                        $jsondata = json_decode($personalization->getPersonalizationJsonData());
                    }
                    else{
                        $jsonFactory->setData(['template_data' => null,'error'=>false]);
                        return $jsonFactory;
                    }
                }
            }
            if($jsondata){
                $jsonFactory->setData(['template_data' => $jsondata,'error'=>false]);
                return $jsonFactory;
            }
            else{
                $jsonFactory->setData(['error' => true]);
                return $jsonFactory;
            }
        } catch (\Exception $e) {
            $response['message']= $e->getMessage();
        }
    }
}
