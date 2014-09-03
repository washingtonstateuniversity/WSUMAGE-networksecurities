<?php
class Wsu_Networksecurities_Block_Sso_Wordpresslogin extends Mage_Core_Block_Template {
    public function getLoginUrl() {
		return $this->getUrl('sociallogin/wordpresslogin/login');
	}
    public function getAlLoginUrl() {
        return $this->getUrl('sociallogin/wordpresslogin/setBlogName');
    }
    public function getCheckName() {        
        return $this->getUrl('sociallogin/wordpresslogin/form');
    }
	public function getEnterName() {
		return 'ENTER YOUR BLOG NAME';
	}
	public function getName() {
		return 'Name';
	}
}