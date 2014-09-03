<?php
class Wsu_Networksecurities_Model_Sso_Aollogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	

	public function createProvider() {
		try{
			require_once(Mage::getBaseDir('lib').DS.'OpenId'.DS.'openid.php');    
		}catch(Exception $e) {}
		$openid = new LightOpenID(Mage::getUrl());    
        return $openid;
	}
	
	public function getLoginUrl($name="") {
		$aol_id = $this->getProvider();
        $aol = $this->setIdlogin($aol_id, $name);
        try{
            $loginUrl = $aol->authUrl();
            return $loginUrl;
        }catch(Exception $e) {
            return null;
        }
	}
    public function setIdlogin($openid, $name) {
        $openid->identity = 'https://openid.aol.com/'.$name;
        $openid->required = array(
			'namePerson/first',
			'namePerson/last',
			'namePerson/friendly',
			'contact/email',
        );

        $openid->returnUrl = Mage::getUrl('sociallogin/aollogin/login');
		return $openid;
    }
	public function getLaunchUrl() {
		return Mage::getUrl("sociallogin/aollogin/form");
	}
}
  
