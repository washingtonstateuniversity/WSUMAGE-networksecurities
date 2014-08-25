<?php

class Wsu_Networksecurities_Model_Sso_Status extends Varien_Object {
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray() {
        return array(
            self::STATUS_ENABLED    => Mage::helper('wsu_networksecurities')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('wsu_networksecurities')->__('Disabled')
        );
    }
}