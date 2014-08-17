<?php
class Wsu_Networksecurities_Block_Sso_Aol extends Mage_Core_Block_Template {
    public function getLoginUrl() {
		return $this->getUrl('sociallogin/allogin/login');
	}
    public function getAlLoginUrl() {
        return Mage::getModel('wsu_networksecurities/sso_allogin')->getAlLoginUrl();
    }
}