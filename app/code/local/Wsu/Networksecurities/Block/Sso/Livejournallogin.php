<?php
class Wsu_Networksecurities_Block_Sso_Livejournallogin extends Mage_Core_Block_Template
{
	public function getLoginUrl() {
		return $this->getUrl('sociallogin/livejournallogin/login');
		//return Mage::getModel('wsu_networksecurities/sso_mylogin')->getMyLoginUrl();
	}
    
    public function getSetBlock() {
        return $this->getUrl('sociallogin/livejournallogin/form');        
    }
	
	public function setBackUrl() {
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		Mage::getSingleton('core/session')->setBackUrl($currentUrl);
		return $currentUrl;
	}
	
		
}