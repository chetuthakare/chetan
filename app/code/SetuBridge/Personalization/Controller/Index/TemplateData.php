<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
class TemplateData extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Context $context,
        \SetuBridge\PersonalizationTemplate\Model\PersonalizationTemplateFactory $personalizationFactory
    ) 
    {
        parent::__construct($context);
        $this->personalizationFactory = $personalizationFactory;
    }

    public function execute()
    {    
        try {
            $templateId = (int)$this->getRequest()->getParam('templateID');
            $jsonFactory=$this->resultFactory->create(ResultFactory::TYPE_JSON);
            if($templateId != 0){
                $personalization  = $this->personalizationFactory->create()->load($templateId);
                $jsondata = json_decode($personalization->getPersonalizationJsonData());
            }
            else{
                $jsonFactory->setData(['error' => false]);
                return $jsonFactory;
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

