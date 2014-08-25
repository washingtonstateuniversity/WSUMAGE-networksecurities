<?php
class Wsu_Networksecurities_Block_Sso_Fblogin extends Mage_Core_Block_Template
{
	public function getLoginUrl() {
		return $this->getUrl('sociallogin/fblogin/login');
	}
	
	public function getFbUser() {
		return Mage::getModel('wsu_networksecurities/sso_fblogin')->getFbUser();
	}
	
	public function getFbLoginUrl() {
		return Mage::getModel('wsu_networksecurities/sso_fblogin')->getFbLoginUrl();
	}
	
	public function getDirectLoginUrl() {
		return Mage::helper('wsu_networksecurities/customer')->getDirectLoginUrl();
	}
	
		
}