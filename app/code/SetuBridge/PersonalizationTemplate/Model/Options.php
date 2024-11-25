<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Model;

class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $_collectionFactory;

    public function __construct(
        \SetuBridge\PersonalizationTemplate\Model\ResourceModel\PersonalizationTemplate\CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    public function getAllOptions()
    {
        $collection = $this->_collectionFactory->create()->addFieldToSelect(array('entity_id','title','personalization_json_data'))
        ->addFieldToFilter('is_active','1');
        $this->_options = [];
        $this->_options[] = array(
            'label' => 'Blank/Custom Template',
            'value' => 0
        );

        foreach ($collection as $category) {
            if($category->getPersonalizationJsonData() && !(empty($category->getPersonalizationJsonData()))){
                $this->_options[] = array(
                    'label' => $category->getTitle(),
                    'value' => $category->getEntityId()
                );
            }
        }

        return $this->_options;

    }

}