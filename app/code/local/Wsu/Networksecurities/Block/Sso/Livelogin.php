<?php
class Wsu_Networksecurities_Block_Sso_Livelogin extends Mage_Core_Block_Template
{
	public function getLoginUrl() {
		return $this->getUrl('sociallogin/fqlogin/login');
	}
	
	public function getFqUser() {
		return Mage::getModel('wsu_networksecurities/sso_fqlogin')->getFqUser();
	}
	
	public function getUrlAuthorCode() {
		return Mage::getModel('wsu_networksecurities/sso_livelogin')->getUrlAuthorCode();
	}
	
	public function getDirectLoginUrl() {
		return Mage::helper('wsu_networksecurities')->getDirectLoginUrl();
	}
	
		
		
}