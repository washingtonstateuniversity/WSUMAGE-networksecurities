<?php
class Wsu_Networksecurities_Model_Sso_Clavidlogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	public function createProvider() {	
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'OpenId'.DS.'openid.php';
		}catch(Exception $e) {}
		
		$openid = new LightOpenID(Mage::getUrl());       
		return $openid;
	}
	public function getLoginUrl($name_blog="") {
		$cal_id = $this->getProvider();
        $cal = $this->setIdlogin($cal_id, $name_blog);
        try{
            $loginUrl = $cal->authUrl();
            return $loginUrl;
        }catch(Exception $e) {
            return null;
        }
	}
    public function setIdlogin($openid, $name_blog) {
        
        $openid->identity = 'https://'.$name_blog.'.clavid.com';
        $openid->required = array(
			'namePerson/first',
			'namePerson/last',
			'namePerson/friendly',
			'contact/email',
        );
        
        $openid->returnUrl = Mage::getUrl('sociallogin/clavidlogin/login');
		return $openid;
    }
    
    public function getIndexAllogin() {
        return Mage::getUrl('sociallogin/clavidlogin/setUserdomain');
    }
}
  
