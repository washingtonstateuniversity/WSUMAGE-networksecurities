<?php
class Wsu_Networksecurities_Block_Sso_Clavidlogin extends Mage_Core_Block_Template {
    public function getLoginUrl() {
		return $this->getUrl('sociallogin/clavidlogin/login');
	}
    public function getAlLoginUrl() {
        return $this->getUrl('sociallogin/clavidlogin/setClaivdName');
    }
    public function getCheckName() {
        return $this->getUrl('sociallogin/clavidlogin/form');        
    }
	public function getName() {
		return 'Name';
	}
	public function getEnterName() {
		return 'ENTER YOUR CLAVID NAME';
	}
}