<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();


/* Admin logging */
$table = $installer->getConnection()
    ->newTable($installer->getTable('newtworksecurities/wsu_spamlog'))
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Type')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'unsigned'  => true,
        'primary'   => true,
        ), 'Value')
    ->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Count')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Count')
    ->addColumn('count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Count')
    ->addColumn('ip', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
        'unsigned'  => true,
        'primary'   => true,
        'default'   => '0.0.0.0',
        ), 'Ip')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addColumn('httpbl_response', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
        'unsigned'  => true,
        'primary'   => true,
        'default'   => 'NXDOMAIN',
        ), 'httpbl_response')
    ->addColumn('user_agent', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
        'unsigned'  => true,
        'primary'   => true,
        'default'   => 'unknown',
        ), 'user_agent')
    ->addColumn('blocked', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(), 'blocked')
	->addColumn('reported_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Reported Time')
    ->setComment('Count Login Attempts');
$installer->getConnection()->createTable($table);



/* STOCK LOGGING
$tableHistory  = $installer->getTable('stockhistory');
$tableItem      = $installer->getTable('cataloginventory_stock_item');
$tableUser      = $installer->getTable('admin/user');

$installer->run("
	DROP TABLE IF EXISTS {$tableHistory};
	CREATE TABLE {$tableHistory} (
	`history_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`item_id` INT( 10 ) UNSIGNED NOT NULL ,
	`user` varchar(40) NOT NULL DEFAULT '',
	`user_id` mediumint(9) unsigned DEFAULT NULL,
	`qty` DECIMAL( 12, 4 ) NOT NULL default '0',
	`is_in_stock` TINYINT( 1 ) UNSIGNED NOT NULL default '0',
	`message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	`created_at` DATETIME NOT NULL ,
	INDEX ( `item_id` )
	) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
");

$installer->getConnection()->addConstraint('FK_STOCK_HISTORY_ITEM', $tableHistory, 'item_id', $tableItem, 'item_id');
$installer->run("
    ALTER TABLE `{$tableHistory}`
        ADD COLUMN `is_admin` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `user_id`;
");
 */

$installer->endSetup();
















