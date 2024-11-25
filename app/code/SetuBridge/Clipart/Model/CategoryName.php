<?php
/**
* Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
?>
<?php

namespace SetuBridge\Clipart\Model;

class CategoryName implements \Magento\Framework\Data\OptionSourceInterface {

    protected $_collectionFactory;

    protected $_options;


    public function __construct(
        \SetuBridge\Clipart\Model\ClipartCategoriesFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    public function toOptionArray() {

        $collection = $this->_collectionFactory->create()->getCollection()->addFieldToSelect(array('clipartcategories','clipartcategories_id'));

        $this->_options = [];

        foreach ($collection as $category) {

            $this->_options[] = array(
                'label' => $category->getClipartcategories(),
                'value' => $category->getClipartcategoriesId()
            );
        }

        return $this->_options;
    }
}
