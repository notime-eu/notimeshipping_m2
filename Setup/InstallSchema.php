<?php

namespace Notime\Shipping\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'notime_shipment_id',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Notime Shipment ID',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'notime_timewindow_date',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Notime Shipment Date',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'notime_service_id',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Notime Shipment Service ID',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'notime_shipment_id',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Notime Shipment ID',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'notime_shipment_time',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Notime Shipment Time',
            ]
        );

        $installer->endSetup();
    }
}