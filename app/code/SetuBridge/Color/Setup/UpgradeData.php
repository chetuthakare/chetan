<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Color\Setup;

use Magento\Framework\Setup;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements Setup\UpgradeDataInterface 
{
    /**
    * EAV setup factory
    *
    * @var EavSetupFactory
    */
    private $_eavSetupFactory;
    /**
    * @param EavSetupFactory  $eavSetupFactory
    */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(
        Setup\ModuleDataSetupInterface $setup,
        Setup\ModuleContextInterface $moduleContext
    ) {
        if(version_compare($moduleContext->getVersion(), '1.0.5', '<')) {
            $setup->startSetup();
            $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'personalization_colors',
                'note',
                "Choose specific colors if you want to restrict color options on product level."
            );
            $setup->endSetup();
        }
    }
}