<?php 
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Clipart\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface 
{
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $tableName = $setup->getTable('personalization_clipart');
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'sample_data_status' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Sample Data Status',
                ],
            ];
            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) 
                $connection->addColumn($tableName, $name, $definition);
            $setup->getConnection()->changeColumn(
                $setup->getTable('personalization_clipart'),
                'position',
                'position',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length'   => 255,
                    'nullable' => false,
                    'comment'  => 'Change data type'
                ]
            );
        }
        if ($setup->getConnection()->isTableExists("personalization_clipartcategories") == true) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('personalization_clipartcategories'),
                'position',
                'position',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length'   => 255,
                    'nullable' => false,
                    'comment'  => 'Change data type'
                ]
            );
        }
        $setup->endSetup();
    }
}