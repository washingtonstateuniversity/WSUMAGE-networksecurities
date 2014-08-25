<?php
class Wsu_Networksecurities_Block_Sso_Selogin extends Mage_Core_Block_Template
{
	
	public function getSeLoginUrl() {
		return $this->getUrl('sociallogin/selogin/login');
	}
}