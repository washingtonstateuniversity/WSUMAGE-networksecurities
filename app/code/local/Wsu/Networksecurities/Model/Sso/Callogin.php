<?php
class Wsu_Networksecurities_Model_Sso_Callogin extends Mage_Core_Model_Abstract {
	public function newCal() {	
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'OpenId'.DS.'openid.php';
		}catch(Exception $e) {}
		
		$openid = new LightOpenID(Mage::getUrl());       
		return $openid;
	}
	public function getCalLoginUrl($name_blog) {
		$cal_id = $this->newCal();
        $cal = $this->setCalIdlogin($cal_id, $name_blog);
        try{
            $loginUrl = $cal->authUrl();
            return $loginUrl;
        }catch(Exception $e) {
            return null;
        }
	}
    public function setCalIdlogin($openid, $name_blog) {
        
        $openid->identity = 'https://'.$name_blog.'.clavid.com';
        $openid->required = array(
			'namePerson/first',
			'namePerson/last',
			'namePerson/friendly',
			'contact/email',
        );
        
        $openid->returnUrl = Mage::getUrl('sociallogin/callogin/login');
		return $openid;
    }
    
    public function getIndexAllogin() {
        return Mage::getUrl('sociallogin/callogin/setUserdomain');
    }
}
  
