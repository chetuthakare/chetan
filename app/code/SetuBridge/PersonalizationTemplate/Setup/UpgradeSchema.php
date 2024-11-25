<?php 
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
namespace SetuBridge\PersonalizationTemplate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;

use Magento\Framework\Setup\ModuleContextInterface;

use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface

{

    public function upgrade(SchemaSetupInterface $setup,

        ModuleContextInterface $context){

        $setup->startSetup();

        // Get module table

        $tableName = $setup->getTable('personalization_shape');

        // Check if the table already exists

        if ($setup->getConnection()->isTableExists($tableName) == true) {

            // Declare data

            $columns = [

                'sample_data_status' => [

                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,

                    'nullable' => true,

                    'comment' => 'Sample Data Status',

                ],

            ];

            $connection = $setup->getConnection();

            foreach ($columns as $name => $definition) {

                $connection->addColumn($tableName, $name, $definition);

            }


        }

        // Get module table

        $tableName = $setup->getTable('personalization_font');

        // Check if the table already exists

        if ($setup->getConnection()->isTableExists($tableName) == true) {

            // Declare data

            $columns = [

                'sample_data_status' => [

                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,

                    'nullable' => true,

                    'comment' => 'Sample Data Status',

                ],

            ];

            $connection = $setup->getConnection();

            foreach ($columns as $name => $definition) {

                $connection->addColumn($tableName, $name, $definition);

            }


        }

        $setup->endSetup();

    }
}