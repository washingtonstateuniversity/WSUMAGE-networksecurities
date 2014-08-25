<?php
class Wsu_Networksecurities_Block_Sso_Mplogin extends Mage_Core_Block_Template
{	
	public function getUrlAuthorCode() {
		return Mage::getModel('wsu_networksecurities/sso_mplogin')->getUrlAuthorCode();
	}
	
	public function getDirectLoginUrl() {
		return Mage::helper('wsu_networksecurities/customer')->getDirectLoginUrl();
	}
	
		
		
}