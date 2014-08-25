<?php
class Wsu_Networksecurities_Model_Sso_Allogin extends Mage_Core_Model_Abstract {
	public function newAol() {	
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'OpenId'.DS.'openid.php';           
		}catch(Exception $e) {}
        $openid = new LightOpenID(Mage::getUrl());    
        return $openid;
	}
	public function getAlLoginUrl($name) {
		$aol_id = $this->newAol();
        $aol = $this->setAolIdlogin($aol_id, $name);
        try{
            $loginUrl = $aol->authUrl();
            return $loginUrl;
        }catch(Exception $e) {
            return null;
        }
	}
    public function setAolIdlogin($openid, $name) {
        $openid->identity = 'https://openid.aol.com/'.$name;
        $openid->required = array(
			'namePerson/first',
			'namePerson/last',
			'namePerson/friendly',
			'contact/email',
        );

        $openid->returnUrl = Mage::getUrl('sociallogin/allogin/login');
		return $openid;
    }
}
  
