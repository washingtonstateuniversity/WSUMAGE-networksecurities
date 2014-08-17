<?php
class Wsu_Networksecurities_Block_Sso_Orglogin extends Mage_Core_Block_Template
{
    public function getLoginUrl() {
		return $this->getUrl('sociallogin/orglogin/login');
	}	
	
    public function getAlLoginUrl() {
        return Mage::getModel('wsu_networksecurities/sso_orglogin')->getOrgLoginUrl();
    }
}