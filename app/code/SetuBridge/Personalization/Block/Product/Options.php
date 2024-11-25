<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Personalization\Block\Product;

use Magento\Catalog\Model\ProductRepository;

class Options extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        ProductRepository $productRepository,
        array $data = []
    ) {
        $this->_registry=$registry;
        $this->productRepository = $productRepository;
        parent::__construct($context,$data);
    }
    public function getProduct(){
        
        return $this->_registry->registry('current_product'); 

    }

}

