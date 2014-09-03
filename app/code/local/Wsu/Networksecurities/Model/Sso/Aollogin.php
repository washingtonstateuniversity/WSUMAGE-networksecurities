<?php
class Wsu_Networksecurities_Model_Sso_Aollogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	

	public function createProvider() {
		try{
			Mage::getBaseDir('lib').DS.'OpenId'.DS.'openid.php';    
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

        $openid->returnUrl = Mage::getUrl('sociallogin/allogin/login');
		return $openid;
    }
	
	public function setBlockAction() {             
       /* $template =  $this->getLayout()->createBlock('sociallogin/aollogin')
                ->setTemplate('sociallogin/au_al.phtml')->toHtml();
        echo $template;*/
		$this->loadLayout();
		$this->renderLayout();
    }
   
    public function setScreenNameAction() {
        $data = $this->getRequest()->getPost();		
		$name = $data['name'];
        if($name) {            
            $url = Mage::getModel('wsu_networksecurities/sso_allogin')->getLoginUrl($name);			
            $this->_redirectUrl($url);
        }else{ 
			Mage::getSingleton('core/session')->addError('Please enter Blog name!');	
			Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
        }
    }
}
  
