<?php
class Wsu_Networksecurities_Block_Sso_Fqlogin extends Mage_Core_Block_Template {
	public function getLoginUrl() {
		return $this->getUrl('sociallogin/fqlogin/login');
	}
	
	public function getFqUser() {
		return Mage::getModel('wsu_networksecurities/sso_fqlogin')->getFqUser();
	}
	
	public function getFqLoginUrl() {
		return Mage::getModel('wsu_networksecurities/sso_fqlogin')->getFqLoginUrl();
	}
	
	public function getDirectLoginUrl() {
		return Mage::helper('wsu_networksecurities')->getDirectLoginUrl();
	}
	
		
		
}