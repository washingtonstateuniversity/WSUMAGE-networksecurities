<?php
class Wsu_Networksecurities_Model_Sso_Livelogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	var $_providerName = 'live';
	public function getAppkey() {	
		return trim(Mage::getStoreConfig('wsu_networksecurities/live_login/consumer_key'));
	}
	public function getAppSecret() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/live_login/consumer_secret'));
	}
    public function getAuthUrl() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return Mage::getUrl('sociallogin/livelogin/login', array('_secure'=>$isSecure, 'auth'=>1));
	}

	      
    public function createProvider() {
		try{			
            require_once(Mage::getBaseDir('lib').DS.'Author'.DS.'OAuth2Client.php');
		}catch(Exception $e) {}
        try{
            $live = new OAuth2Client(
                    $this->getAppkey(),                  
                    $this->getAppSecret(),
                    $this->getAuthUrl()
                    );    
			$live->api_base_url     = "https://apis.live.net/v5.0/";
			$live->authorize_url    = "https://login.live.com/oauth20_authorize.srf";
			$live->token_url        = "https://login.live.com/oauth20_token.srf";			
			$live->out 			    = "https://login.live.com/oauth20_logout.srf";	
            return $live;
        }catch(Exception $e) {}
    }
	
    public function getUrlAuthorCode() {
        $live = $this->getProvider();
        return $live->authorizeUrl();
    }
}

  
