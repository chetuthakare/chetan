<?php
          /** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;

class Products extends \Magento\Backend\App\Action
{
    
    private $resultLayoutFactory;

    
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    
    public function _isAllowed()
    {
        return true;
    }

    
    public function execute()
    {

        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('personalization.edit.tab.products');

        return $resultLayout;
    }
}
