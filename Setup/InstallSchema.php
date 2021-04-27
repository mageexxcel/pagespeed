<?php

namespace Excellence\Pagespeed\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        
        /**
         * Create table 'excellence_pagespeed'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('excellence_pagespeed')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'excellence_pagespeed'
        )
        ->addColumn(
            'gtmetrix_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Gtmetrix Id'
        )
        ->addColumn(
            'onload_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'onload_time'
        )
        ->addColumn(
            'page_load_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'page_load_time'
        )
        ->addColumn(
            'fully_loaded_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'fully_loaded_time'
        )
        ->addColumn(
            'yslow_score',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'yslow_score'
        )
        ->addColumn(
            'pagespeed_score',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'pagespeed_score'
        )
        ->addColumn(
            'backend_duration',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'backend_duration'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )
        ->setComment(
            'Excellence Pagespeed excellence_pagespeed'
        );
		
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
	}
}
