<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
class Index extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {    
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getLayout()->getUpdate()->removeHandle('default_head_blocks');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Personlization'));
        return $resultPage;
    }

} 

