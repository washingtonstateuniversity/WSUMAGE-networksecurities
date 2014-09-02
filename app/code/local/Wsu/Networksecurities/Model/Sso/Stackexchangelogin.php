<?php
class Wsu_Networksecurities_Model_Sso_Stackexchangelogin extends Mage_Core_Model_Abstract {
	public function newProvider() {	
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'OpenId'.DS.'openid.php';           
		}catch(Exception $e) {}
        
        $openid = new LightOpenID(Mage::getUrl());    
        return $openid;
	}
	public function getLoginUrl($name="") {
		$aol_id = $this->newProvider();
        $aol = $this->setIdlogin($aol_id, $name);
        try{
            $loginUrl = $aol->authUrl();
            return $loginUrl;
        }catch(Exception $e) {
            return null;
        }
	}
    public function setIdlogin($openid) {
        $openid->identity = 'https://openid.stackexchange.com';
        $openid->required = array(
			'namePerson/first',
			'namePerson/last',
			'namePerson/friendly',
			'contact/email',
        );
        $openid->returnUrl = Mage::getUrl('sociallogin/stackexchangelogin/login');
		return $openid;
    }
}
  
