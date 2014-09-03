<?php

class Wsu_Networksecurities_Model_Sso_Abstract extends Mage_Core_Model_Abstract {

	var $_provider;

	public function getProvider() {
		if(!isset($this->_provider)){
			$this->_provider = $this->createProvider();
		}
		return $this->_provider;
	}
	
	
}