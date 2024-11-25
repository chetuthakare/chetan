<?php 
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\Personalization\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;

use Magento\Framework\Setup\ModuleContextInterface;

use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface

{

    public function upgrade(SchemaSetupInterface $setup,

        ModuleContextInterface $context){

        $setup->startSetup();

        // Get module table

        $tableName = $setup->getTable('quote_item');

        // Check if the table already exists

        if ($setup->getConnection()->isTableExists($tableName) == true) {

            // Declare data

            $columns = [
                'personalization_item_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    100,
                    'nullable' => false,
                    'comment' => 'Personalization Item Id',
                ],
                'is_personalization_item' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    'nullable' => false,
                    'comment' => 'is Personalization Item',
                ],

            ];


            $connection = $setup->getConnection();

            foreach ($columns as $name => $definition) {

                $connection->addColumn($tableName, $name, $definition);

            }


        }

        $tableName = $setup->getTable('sales_order_item');

        if ($setup->getConnection()->isTableExists($tableName) == true) {

            // Declare data

            $columns = [
                'is_personalization_item' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    'nullable' => false,
                    'comment' => 'is Personalization Item',
                ],

            ];


            $connection = $setup->getConnection();

            foreach ($columns as $name => $definition) {

                $connection->addColumn($tableName, $name, $definition);

            }


        }

        $tableName = $setup->getTable('personalization_order_data');

        if (!$setup->tableExists('personalization_order_data')) {
            $table = $setup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Order Id'
            )->addColumn(
                'order_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Order Item Id'
            )->addColumn(
                'quote_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Quote Item Id'
            )->addColumn(
                'personalization_json_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '1G',
                [],
                'Personalization Json Data'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At')
            ->setComment('PersonalizationTemplate Shape Table');

            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();

    }
}
