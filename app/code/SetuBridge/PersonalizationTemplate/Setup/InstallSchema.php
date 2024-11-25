<?php
/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

namespace SetuBridge\PersonalizationTemplate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('personalization_shape');

        if (!$installer->tableExists('personalization_shape')) {
            $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'shape_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Shape Id'
            )->addColumn(
                'shape_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Shape Name'
            )->addColumn(
                'shape_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Shape Image'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                1,
                [],
                'Shape Status'
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

            $installer->getConnection()->createTable($table);
        }

        $tableName = $installer->getTable('personalization_font');

        if (!$installer->tableExists('personalization_font')) {
            $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'font_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Font Id'
            )->addColumn(
                'font_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Shape Name'
            )->addColumn(
                'font_file',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Font File'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                1,
                [],
                'Shape Status'
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
            ->setComment('PersonalizationTemplate Font Table');

            $installer->getConnection()->createTable($table);
        }


        $tableName = $installer->getTable('product_editpage_data');

        if (!$installer->tableExists('product_editpage_data')) {
            $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Id'
            )->addColumn(
                'template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Template Id'
            )->addColumn(
                'personalization_json_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '1G',
                [],
                'Personalization Json Data'
            )->addColumn(
                'uniqid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'UniqId'
            )
            ->setComment('Product Editpage Data');

            $installer->getConnection()->createTable($table);
        }

        $tableName = $installer->getTable('personalization_data');

        if (!$installer->tableExists('personalization_data')) {
            $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'category',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Category'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Title'
            )->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Active Status'
            )->addColumn(
                'personalization_json_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '512M',
                [],
                'PersonalizationTemplate Json Data'
            )->addColumn(
                'unique_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Unique Id'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                ],
                'Creation Time'
            )->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Modification Time'
            )->setComment(
                'Row Data Table'
            );

            $installer->getConnection()->createTable($table);
        }


        $installer->endSetup();
    }
}
