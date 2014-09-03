<?php
class Wsu_Networksecurities_Sso_ClavidloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {
	
	/**
	* getToken and call profile user Clavid
	**/
    public function loginAction($name_blog) {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$cal = Mage::getModel('wsu_networksecurities/sso_clavidlogin')->newProvider();       
		$userId = $cal->mode;        
		$coreSession = Mage::getSingleton('core/session');
		if(!$userId) {
            $cal_session = Mage::getModel('wsu_networksecurities/sso_clavidlogin')->setIdlogin($cal, $name_blog);
            $url = $cal_session->authUrl();
			$this->_redirectUrl($url);
		}else{ 
			if ($cal->validate()) {                
				$user_info = $cal->getAttributes();                 
                if(count($user_info)) {
					$user_info['provider']="clavid";
					$this->handleCustomer($user_info);
                }
            }
			Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access.'));
			$customerHelper->setJsRedirect(Mage::getBaseUrl());          
        }
    }

    public function setBlockAction() {             
        /*$template =  $this->getLayout()->createBlock('sociallogin/clavidlogin')
                ->setTemplate('sociallogin/au_cal.phtml')->toHtml();
        echo $template;*/
		$this->loadLayout();
		$this->renderLayout();
    }
    
    public function setClaivdNameAction() {
        $data = $this->getRequest()->getPost();
        if($data) {
            $name = $data['name'];
            $url = Mage::getModel('wsu_networksecurities/sso_clavidlogin')->getCalLoginUrl($name);
            $this->_redirectUrl($url);
        }else{ 
			Mage::getSingleton('core/session')->addError('Please enter Blog name!');	
            Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
        }
 
   }
}