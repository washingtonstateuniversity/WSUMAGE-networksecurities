<?php
class Wsu_Networksecurities_Block_Sso_Twlogin extends Mage_Core_Block_Template
{
	public function getLoginUrl() {
		return $this->getUrl('sociallogin/twlogin/login');
	}
	
	public function setBackUrl() {
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		Mage::getSingleton('core/session')->setBackUrl($currentUrl);
        //Zend_debug::dump($currentUrl);
		return $currentUrl;
	}
	
		
}