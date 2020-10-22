<?php


namespace Magehit\Storelocator\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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

        $table_magehit_storelocator_storelocator = $setup->getConnection()->newTable($setup->getTable('magehit_storelocator_storelocator'));

        
        $table_magehit_storelocator_storelocator->addColumn(
            'storelocator_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        
        
        $table_magehit_storelocator_storelocator->addColumn(
            'store_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'store_name'
        );
        
        $table_magehit_storelocator_storelocator->addColumn(
            'store_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'Url'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'store_thumnail',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'store_thumnail'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'email'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'website',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'website'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'lat',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'lat'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'lng',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'lng'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'street',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'street'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'city'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'region',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'region'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'postcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'postcode'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'country'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'telephone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'telephone'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'fax',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'fax'
        );
        

        
        $table_magehit_storelocator_storelocator->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'content'
        );
        
        $table_magehit_storelocator_storelocator->addColumn(
            'schedule',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'schedule'
        );
        $table_magehit_storelocator_storelocator->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => true],
                'Location Status'
            );
        $table_magehit_storelocator_storelocator->addColumn(
            'in_store',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '0'],
            'Store'
        );
        $table_magehit_storelocator_storelocator->addColumn(
                'store_schedule',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
            ['nullable' => false, 'default' => 1],
                'Store Schedule'
        );
        $table_magehit_storelocator_storelocator->addColumn(
            'product_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'product_ids'
        );
        

        $setup->getConnection()->createTable($table_magehit_storelocator_storelocator);

        $setup->endSetup();
    }
}
