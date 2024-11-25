<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Color\Model;

class ColorAttributeoptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    protected $_collectionFactory;

    protected $_options;

    public function __construct(
        \SetuBridge\Color\Model\DataFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    public function getAllOptions() {

        $collection = $this->_collectionFactory->create()->getCollection()
        ->addFieldToSelect(array('color_code','color_name'));

        $this->_options = [];
        $this->_options[] = array(
            'label' => 'No selected',
            'value' => 0
        );
    
        foreach ($collection as $category) {

            $this->_options[] = array(
                'label' => $category->getColorName(),
                'value' => $category->getColorCode()
            );
        }

        return $this->_options;
    }
}