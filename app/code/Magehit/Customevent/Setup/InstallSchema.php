<?php
namespace Magehit\Customevent\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        // quote
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'presenter',
            [
                'type' =>\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'comment' => 'presenter',
				'nullable' => true,
				'default' => 0
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'ambassador',
            [
                'type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'ambassador',
            ]
        );
        // order
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'presenter',
            [
                'type' =>\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'comment' => 'presenter',
				'nullable' => true,
				'default' => 0
            ]
        );
		$setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'ambassador',
            [
                'type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'ambassador',
            ]
        );
        $setup->endSetup();
    }
}
