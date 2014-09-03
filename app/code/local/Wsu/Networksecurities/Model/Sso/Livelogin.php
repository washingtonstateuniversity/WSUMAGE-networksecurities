<?php
class Wsu_Networksecurities_Model_Sso_Livelogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	      
    public function createProvider() {
		try{			
            Mage::getBaseDir('lib').DS.'Author'.DS.'OAuth2Client.php';
		}catch(Exception $e) {}
        try{
            $live = new OAuth2Client(
                    Mage::helper('wsu_networksecurities/customer')->getLiveAppkey(),                  
                    Mage::helper('wsu_networksecurities/customer')->getLiveAppSecret(),
                    Mage::helper('wsu_networksecurities/customer')->getAuthUrlLive()
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

  
