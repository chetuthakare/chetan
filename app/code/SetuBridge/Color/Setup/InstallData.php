<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\Color\Setup;

use SetuBridge\Color\Model\Data;
use SetuBridge\Color\Model\DataFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{

    private $dataFactory;

    public function __construct(
        DataFactory $dataFactory,
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->dataFactory = $dataFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $data = array(
            'group' => 'Personalization',
            'type' => 'text',
            'input' => 'multiselect',
            'label' => 'Colors',
            'sort_order' => 6,
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'frontend' => '',
            'source' => 'SetuBridge\Color\Model\ColorAttributeoptions',
            'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'note' => 'Choose specific colors if you want to restrict color options on product level.',
            'apply_to' => ''
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'personalization_colors',
            $data);
    }
}
