<?php

class Wsu_NewtworkSecurities_Adminhtml_Block_Honeypot extends Mage_Core_Block_Template {
	protected function _construct(){
        parent::_construct();
    }
    public function getHoneypotName(){
        $helper = Mage::helper('wsu_newtworksecurities');
        return $helper->getHoneypotName();
    }
}
