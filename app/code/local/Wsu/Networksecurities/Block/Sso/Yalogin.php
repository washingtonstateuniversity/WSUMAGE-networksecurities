<?php
class Wsu_Networksecurities_Block_Sso_Yalogin extends Mage_Core_Block_Template
{
	public function getLoginUrl() {
		return $this->getUrl('sociallogin/yalogin/login');
	}
	
		
}