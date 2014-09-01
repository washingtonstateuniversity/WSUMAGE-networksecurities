<?php

class Wsu_Networksecurities_Sso_AolloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {

    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$aol = Mage::getModel('wsu_networksecurities/sso_aollogin')->newProvider();       
		$userId = $aol->mode;        
		$coreSession = Mage::getSingleton('core/session');		
		if(!$userId) {
            $aol_session = Mage::getModel('wsu_networksecurities/sso_aollogin')->setAolIdlogin($aol);
            $url = $aol_session->authUrl();
			$this->_redirectUrl($url);
		}else{
			if (!$aol->validate()) {                
				$aol_session = Mage::getModel('wsu_networksecurities/sso_aollogin')->setAolIdlogin($aol);
                $url = $aol_session->authUrl();
                $this->_redirectUrl($url);
            }else{
				$user_info = $aol->getAttributes();                 
                if(count($user_info)) {
					$user_info['provider']="aol";
					$this->handleCustomer($user_info);
                }else{ 
					$coreSession->addError($this->__('Login failed as you have not granted access.'));
					$customerHelper->setJsRedirect(Mage::getBaseUrl());
                }
            }           
        }
    }

}