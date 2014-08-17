<?php
class Wsu_Networksecurities_Model_Config_Position {
    public function toOptionArray() {
        return array(
            array('value' => 'header', 'label'=>Mage::helper('adminhtml')->__('Header')),
            array('value' => 'before-customer-login', 'label'=>Mage::helper('adminhtml')->__('Above customer login form')),
            array('value' => 'after-customer-login', 'label'=>Mage::helper('adminhtml')->__('Below customer login form')),
            array('value' => 'before-customer-registration', 'label'=>Mage::helper('adminhtml')->__('Above customer registration form')),
            array('value' => 'after-customer-registration', 'label'=>Mage::helper('adminhtml')->__('Below customer registration form')),        
			array('value' => 'popup', 'label'=>Mage::helper('adminhtml')->__('Show popup when click login')),
		);
    }
}