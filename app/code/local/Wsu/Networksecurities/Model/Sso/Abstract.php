<?php

class Wsu_Networksecurities_Model_Sso_Abstract extends Mage_Core_Model_Abstract {

	var $_provider;

	public function getProvider() {
		if(!isset($this->_provider)){
			$this->_provider = $this->createProvider();
		}
		return $this->_provider;
	}
	public function getLaunchUrl($account=null) {
		$provider = $this->_providerName;
		$queries = array();
		if(isset($account)){
			$queries['account']=$account;
		}
		return Mage::getUrl("sociallogin/${provider}login/login",$queries);
	}
	
}