<?php
class Wsu_Networksecurities_Block_Sso_Callogin extends Mage_Core_Block_Template {
    public function getLoginUrl() {
		return $this->getUrl('sociallogin/callogin/login');
	}	
	
    public function getAlLoginUrl() {
        return $this->getUrl('sociallogin/callogin/setClaivdName');
    }
    
    public function getCheckName() {
        return $this->getUrl('sociallogin/callogin/setBlock');        
    }
	
	public function getName() {
		return 'Name';
	}
	
	public function getEnterName() {
		return 'ENTER YOUR CLAVID NAME';
	}
	

}