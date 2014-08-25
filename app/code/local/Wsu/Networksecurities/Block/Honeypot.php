<?php
class Wsu_Networksecurities_Block_Honeypot extends Mage_Core_Block_Template {
    public function getHoneypotTheme() {
        $helper = Mage::helper('wsu_networksecurities');
		$id = $helper->getHoneypotId();
		$theme['name']=$helper->getHoneypotName($id);
		$theme['ids']=$id;
        return $theme;
    }
}
