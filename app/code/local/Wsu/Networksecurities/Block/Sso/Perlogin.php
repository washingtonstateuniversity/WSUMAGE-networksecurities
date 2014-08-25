<?php
class Wsu_Networksecurities_Block_Sso_Perlogin extends Mage_Core_Block_Template
{

	public function getPerLoginUrl() {
		return $this->getUrl('sociallogin/perlogin/login/');
	}
}