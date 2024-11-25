<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Risecommerce\Sku\Block;

class Product extends \Magento\Framework\View\Element\Template
{
	
	private $resourceConfigurable;
	
	private $resourceProduct;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
	 
	
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $resourceConfigurable,
        array $data = []
    ) {
        $this->resourceProduct = $resourceProduct;
        $this->resourceConfigurable = $resourceConfigurable;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    /* public function getParentProductId($childProductId)
    {
		//print_r($childProductId);die("Hello");
        $parentConfigObject = $this->configurable->getParentIdsByChild($childProductId);
		print_r($parentConfigObject);die;
	    if($parentConfigObject) {
		return $parentConfigObject[0];
	    }
	    return false;
    } */
	
	public function getParentProductId($childSku)
	{
		$childId = $this->resourceProduct->getIdBySku($childSku);
    
        if ($childId) {
    
            $parentIds = $this->resourceConfigurable->getParentIdsByChild($childId);
            if (!empty($parentIds)) {
                $skus = $this->resourceProduct->getProductsSku($parentIds);
	
                return $skus[0]['sku'];
            }
			return false;
    
        }
	}
	
	
}

