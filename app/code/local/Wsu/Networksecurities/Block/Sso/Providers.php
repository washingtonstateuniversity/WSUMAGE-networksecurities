<?php
class Wsu_Networksecurities_Block_Sso_Providers extends Mage_Core_Block_Template {

	public function getLoginUrl() {
		return $this->getUrl('sociallogin/fblogin/login');
	}
	
	public function getUser($provider) {
		return Mage::getModel('wsu_networksecurities/sso_fblogin')->getUser();
	}
	
	public function getBTUrl($provider) {
		return Mage::getModel('wsu_networksecurities/sso_fblogin')->getLoginUrl();
	}
	
	public function getDirectLoginUrl($provider) {
		return Mage::helper('wsu_networksecurities/customer')->getDirectLoginUrl();
	}
    public function getTitle($provider) {
        return (string) Mage::getStoreConfig("wsu_networksecurities/${provider}_login/title",Mage::app()->getStore()->getId());
    }
    public function getText($provider) {
        return (string) Mage::getStoreConfig("wsu_networksecurities/${provider}_login/text",Mage::app()->getStore()->getId());
    }
    public function getWidth($provider) {
        return (int) Mage::getStoreConfig("wsu_networksecurities/${provider}_login/width",Mage::app()->getStore()->getId());
    }
    public function getHeight($provider) {
        return (int) Mage::getStoreConfig("wsu_networksecurities/${provider}_login/height",Mage::app()->getStore()->getId());
    }
	
}