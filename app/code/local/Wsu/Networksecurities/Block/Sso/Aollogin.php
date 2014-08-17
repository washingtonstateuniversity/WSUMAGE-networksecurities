<?php
class Wsu_Networksecurities_Block_Sso_Aollogin extends Mage_Core_Block_Template {
    public function getLoginUrl() {
		return $this->getUrl('sociallogin/allogin/login');
	}	
	
    public function getAlLoginUrl() {
        return $this->getUrl('sociallogin/allogin/setScreenName');
    }
	
	public function getEnterName() {
		return 'ENTER SCREEN NAME';
	}
	
	public function getName() {
		return 'Name';
	}
	
	public function getCheckName() {
		return $this->getUrl('sociallogin/allogin/setBlock');
	}
}