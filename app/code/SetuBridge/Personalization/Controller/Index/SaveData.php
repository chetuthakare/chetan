<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class SaveData extends \Magento\Framework\App\Action\Action
{
    protected $personalizationFactory;
    protected $model;
    protected $_helperData;
    public function __construct(
        Context $context,
        \SetuBridge\PersonalizationTemplate\Model\ProductEditpageData $model,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplateFactory $personalizationFactory,
        \SetuBridge\Personalization\Helper\Data $helperData
    ) 
    {
        parent::__construct($context);
        $this->personalizationFactory = $personalizationFactory;
        $this->model = $model;
        $this->_helperData = $helperData;
    }

    public function execute()
    {    
        try {
            $template_data = $this->getRequest()->getPostValue('template_data');
            $productId = $this->getRequest()->getPostValue('productID');
            $uniqId = $this->getRequest()->getPostValue('uniqId');
            $templateID = $this->getRequest()->getPostValue('templateID');
            $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);

            if($uniqId){
                $this->model->load($uniqId,'uniqid');
                if($this->model->getEntityId()){
                    $this->model->setData('personalization_json_data',$template_data);
                    $this->model->setData('template_id',$templateID);
                }
                else{
                    $this->model->setData('personalization_json_data',$template_data);
                    $this->model->setData('uniqid',$uniqId);
                    $this->model->setData('template_id',$templateID);
                }
            }
            else{
                $this->model->load($productId,'product_id');
                if($this->model->getEntityId()){
                    $this->model->setData('personalization_json_data',$template_data);
                    $this->model->setData('template_id',$templateID);
                }
                else{
                    $this->model->setData('personalization_json_data',$template_data);
                    $this->model->setData('template_id',$templateID);
                    $this->model->setData('product_id',$productId);
                }
            }

            $saveData = $this->model->save();
            
            if($saveData){
                $jsonFactory->setData(['error'=>false]);
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

