<?php


$installer = $this;

$connection = $installer->getConnection();

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('wsu_failedlogin_log'), 'cleared',"TINYINT(1) UNSIGNED DEFAULT 0");

$installer->endSetup();

