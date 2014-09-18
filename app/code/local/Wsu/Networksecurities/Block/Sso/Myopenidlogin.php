<?php
class Wsu_Networksecurities_Block_Sso_Myopenidlogin extends Mage_Core_Block_Template {
	public function getLoginUrl() {
		return $this->getUrl('sociallogin/myopenidlogin/login');
	}
    public function getSetBlock() {
        return $this->getUrl('sociallogin/openlogin/form');        
    }
	public function setBackUrl() {
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		Mage::getSingleton('core/session')->setBackUrl($currentUrl);
		return $currentUrl;
	}
}