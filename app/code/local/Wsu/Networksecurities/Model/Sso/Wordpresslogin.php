<?php
class Wsu_Networksecurities_Model_Sso_Wordpresslogin extends Mage_Core_Model_Abstract {
	public function newProvider() {	
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'OpenId'.DS.'openid.php';
		}catch(Exception $e) {}
		
		$openid = new LightOpenID(Mage::getUrl());       
		return $openid;
	}
	public function getLoginUrl($name_blog) {
		$wp_id = $this->newProvider();
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
}
  
