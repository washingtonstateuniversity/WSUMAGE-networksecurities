<?php


$installer = $this;
/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');

$store = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$installer=new  Mage_Customer_Model_Entity_Setup ('core_setup');
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

$connection->addColumn($this->getTable('wsu_failedlogin_log'), 'cleared',"TINYINT(1) UNSIGNED DEFAULT 0");

	
$installer->endSetup();

