<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Cart;
use Magento\Framework\Controller\ResultFactory;
class Formkey extends \Magento\Framework\App\Action\Action
{  

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        parent::__construct($context);
        $this->formkey=$formKey;
    }

    public function execute()
    {    
        $json = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $json->setData(['form_key'=>$this->formkey->getFormKey()]);
    }

} 

