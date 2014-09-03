<?php
class Wsu_Networksecurities_Model_Sso_Myspacelogin extends Wsu_Networksecurities_Model_Sso_Abstract {      

	public function getConsumerKey() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/myspace_login/consumer_key'));
	}
	public function getConsumerSecret() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/myspace_login/consumer_secret'));
	}
	public function getAuthUr() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return Mage::getUrl('sociallogin/myspacelogin/login', array('_secure'=>$isSecure, 'auth'=>1));
	}
	
    public function createProvider($token = null) {
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Author'.DS.'OAuth.php';
            require_once Mage::getBaseDir('base').DS.'lib'.DS.'Author'.DS.'OAuth1Client.php';
		}catch(Exception $e) {}
        try{
			if ($token) {
				$provider = new OAuth1Client(
                    $this->getConsumerKey(), 					
                    $this->getConsumerSecret(),                    
					$token['oauth_token'],
					$token['oauth_token_secret']
                );    
			}else{
				$provider = new OAuth1Client(
                    $this->getConsumerKey(), 					
                    $this->getConsumerSecret()                  					
                ); 
			} 
			$provider->api_base_url          = "http://api.myspace.com/v1/";
			$provider->authorize_url         = "http://api.myspace.com/authorize";			
			$provider->request_token_url     = "http://api.myspace.com/request_token";
			$provider->access_token_url      = "http://api.myspace.com/access_token";
            return $mp;
        }catch(Exception $e) {}
    }
	
    public function getUrlAuthorCode() {
        $provider = $this->getProvider();		
        $token = $provider->requestToken($this->getAuthUr());			
		Mage::getSingleton('core/session')->setRequestToken($token);
		return  $provider->authorizeUrl($token);		
    }
	

	
}

  
