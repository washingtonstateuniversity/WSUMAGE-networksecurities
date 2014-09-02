<?php
class Wsu_Networksecurities_Model_Sso_Livejournallogin extends Mage_Core_Model_Abstract {
	public function newProvider() {
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'OpenId'.DS.'openid.php';
		}catch(Exception $e) {}
		$openid = new LightOpenID(Mage::getUrl());       
		return $openid;
	}
	public function getLjLoginUrl($identity) {
		$my_id = $this->newProvider();
        $my = $this->setLjIdlogin($my_id,$identity);
		$loginUrl = $my->authUrl();
		return $loginUrl;
	}
	public function setLjIdlogin($openid,$identity) {
        $openid->identity = "http://".$identity.".livejournal.com";
        $openid->required = array(
			'namePerson/first',
			'namePerson/last',
			'namePerson/friendly',
			'contact/email'
        );
        $openid->returnUrl = Mage::getUrl('sociallogin/livejournallogin/login');
		return $openid;
    }
}
  
