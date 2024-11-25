<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Model;

class Categoryoptions implements \Magento\Framework\Data\OptionSourceInterface {
    
    protected $_collectionFactory;

    protected $_options;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    public function toOptionArray() {
       
            $collection = $this->_collectionFactory->create()->addAttributeToSelect(array('id','name'))
        ->addAttributeToFilter('is_active','1');

            $this->_options = [];

            foreach ($collection as $category) {
                $this->_options[] = array(
               'label' => $category->getName(),
               'value' => $category->getId()
            );
            }

        return $this->_options;
    }
}
