<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use SetuBridge\Personalization\Model\PersonalizeProduct;

class Page extends \Magento\Framework\App\Action\Action
{
    protected $personalizeProduct;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PersonalizeProduct $personalizeProduct
    ) {
        parent::__construct($context);
        $this->personalizeProduct = $personalizeProduct;
    }
    public function execute()
    {    
        $json=$this->resultFactory->create(ResultFactory::TYPE_JSON);
        $this->personalizeProduct->setProductCustomOptions($this->getRequest()->getParam('product'),$this->getRequest()->getParam('options'));
        return $json->setData(
            [
                'error'=>false,
                'redirect_url'=>$this->_url->getUrl("personalization/index/editor",['product'=>$this->getRequest()->getParam('product')])
            ]
        );
    }

} 

