<?php
namespace Magehit\Storelocator\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            // Get module table
            $tableName = $setup->getTable('magehit_storelocator_storelocator');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'in' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'comment' => 'in Frontend or Backend',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }

            }
        }
        if (version_compare($context->getVersion(), '1.0.3') < 0) {

			$table_magehit_storelocator_photos = $setup->getConnection()->newTable($setup->getTable('magehit_storelocator_photo'));

			$table_magehit_storelocator_photos->addColumn(
				'photo_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
				'photo ID'
			);
			
			
			$table_magehit_storelocator_photos->addColumn(
				'thumbnai_image',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => false],
				'thumbnai image'
			);
			
			$table_magehit_storelocator_photos->addColumn(
				'image',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				['nullable' => false],
				'image'
			);
			
			$table_magehit_storelocator_photos->addColumn(
				'status',
				\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
				null,
				['nullable' => true],
				'status'
			);
			
			$table_magehit_storelocator_photos->addColumn(
				'name',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[],
				'name'
			);
			
			$table_magehit_storelocator_photos->addColumn(
				'details',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				null,
				[],
				'details'
			);
			
			$table_magehit_storelocator_photos->addColumn(
				'city',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[],
				'city'
			);
			
			$table_magehit_storelocator_photos->addColumn(
				'state',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[],
				'state'
			);
			
			$table_magehit_storelocator_photos->addColumn(
				'country',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[],
				'country'
			);
			
			$setup->getConnection()->createTable($table_magehit_storelocator_photos);
        }

        $setup->endSetup();
    }
}
?>