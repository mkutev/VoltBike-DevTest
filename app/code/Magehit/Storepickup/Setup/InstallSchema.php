<?php


namespace Magehit\Storepickup\Setup;

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
        $table_magehit = $setup->getConnection()->newTable($setup->getTable('magehit_storepickup_storepickup'));
   
        $table_magehit->addColumn(
            'storepickup_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        )->addColumn(
            'store_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'store_name'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'email'
        )->addColumn(
            'street',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'street'
        )->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'city'
        )->addColumn(
            'region',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'region'
        )->addColumn(
            'postcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'postcode'
        )->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'country'
        )->addColumn(
            'telephone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'telephone'
        )->addColumn(
            'schedule',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'schedule'
        )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true],
                'Location Status'
            )->addColumn(
            'in_store',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '0'],
            'Store'
        )->addColumn(
                'store_schedule',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
            ['nullable' => false, 'default' => 1],
                'Store Schedule'
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false),
            'Rule Id'
        )->addColumn(
            'handling_fee',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            null,
            array('nullable' => false, 'scale'=> 1,'precision' =>10),
            'Handling fee'
        );
        $setup->getConnection()->createTable($table_magehit);

        
        //table2
        $table_magehit = $setup->getConnection()
        ->newTable($setup->getTable('magehit_storepickup_rules'))
        ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
        ->addColumn(
                'conditions_serialized',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Conditions Serialized'
            )         
        ->setComment('CatalogRule');
        $setup->getConnection()->createTable($table_magehit);
        //Quote
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'storepickup_data',
            [
                'type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Storepickup Data',
            ]
        );
        //sales_order
        $setup->getConnection()
        ->addColumn(
            $setup->getTable('sales_order'),
            'storepickup_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'Storepickup Id',
            ]
        );
        $setup->getConnection()
        ->addColumn(
            $setup->getTable('sales_order'),
            'storepickup_datetime',
            [
                'type' => 'datetime',
                'nullable' => false,
                'comment' => 'Storepickup Datetime',
            ]
        );

        $setup->endSetup();
    }
}
