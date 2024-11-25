<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class SaveDemo extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \SetuBridge\Personalization\Model\PersonalizationOrderDataFactory $model
    ) 
    {
        $this->modelFactory = $model;
        $this->model = $this->modelFactory->create();
        parent::__construct($context);
    }

    public function execute()
    {    
        try {

            $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);

            $template_data = "gfgffggfgf";
            $itemId = 75;
            $arr = ["pankaj","dinesh","dipen"];
            foreach($arr as $key=>$va){
                /* $this->model->load($itemId,'order_item_id');
                if($this->model->getEntityId()){
                $this->model->setData('personalization_json_data',$template_data);
                }
                else{*/
                echo $va;
                $this->model->setData('personalization_json_data',$va);
                $this->model->setData('order_item_id',$key);
                //                }
                
//                $this->model->unsetData();
            }
            $this->model->save();


        } catch (\Exception $e) {
            $response['message']= $e->getMessage();
        }  
    }
} 

