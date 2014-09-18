<?php
class Wsu_Networksecurities_Model_Sso_Wordpresslogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	var $_providerName = 'wordpress';
	public function createProvider() {	
		try{
			require_once(Mage::getBaseDir('lib').DS.'OpenId'.DS.'openid.php');
		}catch(Exception $e) {}
		
		$openid = new LightOpenID(Mage::getUrl());       
		return $openid;
	}
	public function getLoginUrl($name_blog) {
		$wp_id = $this->getProvider();
        $wp = $this->setIdlogin($wp_id, $name_blog);		
        try{
            $loginUrl = $wp->authUrl();
            return $loginUrl;            
        }  catch (Exception $e) {
            return null;
        }		
	}
    public function setIdlogin($openid, $name_blog) {
        
        $openid->identity = 'http://'. $name_blog . '.wordpress.com';
        $openid->required = array(
			'namePerson/first',
			'namePerson/last',
			'namePerson/friendly',
			'contact/email',
        );
        $openid->returnUrl = Mage::getUrl('sociallogin/wordpresslogin/login');
		return $openid;
    }
	public function getLaunchUrl($account=null) {
		$queries = array();
		if(isset($account)){
			$queries['account']=$account;
		}
		return Mage::getUrl("sociallogin/wordpresslogin/form",$queries);
	}
}
  
