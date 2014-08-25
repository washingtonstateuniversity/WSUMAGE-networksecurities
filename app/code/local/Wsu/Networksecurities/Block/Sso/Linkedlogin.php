<?php
class Wsu_Networksecurities_Block_Sso_Linkedlogin extends Mage_Core_Block_Template
{
	public function getLoginUrl() {
		return $this->getUrl('sociallogin/linkedlogin/login');
	}
	
	/*public function getLoginUrl() {
        return Mage::getModel('wsu_networksecurities/sso_linkedlogin')->getLinkedLoginUrl();
    }*/
	

}