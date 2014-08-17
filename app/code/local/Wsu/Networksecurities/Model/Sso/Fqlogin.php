<?php
class Wsu_Networksecurities_Model_Sso_Fqlogin extends Mage_Core_Model_Abstract {
	public function newFoursquare() {
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Foursquare'.DS.'FoursquareAPI.class.php';
		}catch(Exception $e) {}
		
		$foursquare = new FoursquareApi(
			Mage::helper('wsu_networksecurities/customer')->getFqAppkey(),
			Mage::helper('wsu_networksecurities/customer')->getFqAppSecret(),
            urlencode(Mage::helper('wsu_networksecurities/customer')->getAuthUrlFq())
		);
		return $foursquare;
	}
	public function getFqLoginUrl() {
		$foursquare = $this->newFoursquare();
		$loginUrl = $foursquare->AuthenticationLink();
		return $loginUrl;
	}
}
  
