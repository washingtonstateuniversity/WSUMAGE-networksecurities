<?php

class Wsu_Networksecurities_Sso_SeloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {
	
    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$se = Mage::getModel('wsu_networksecurities/sso_selogin')->newSe(); 	
		$userId = $se->mode;
		$coreSession = Mage::getSingleton('core/session');	
		if(!$userId) {
			$se_session = Mage::getModel('wsu_networksecurities/sso_selogin')->setSeIdlogin($se);
			$url = $se_session->authUrl();
			$this->_redirectUrl($url);
		}else{ 
			if (!$se->validate()) {
				$se_session = Mage::getModel('wsu_networksecurities/sso_selogin')->setSeIdlogin($se);
				$url = $se_session->authUrl();
				$this->_redirectUrl($url);
			}else{ 
				$user_info = $se->getAttributes();
                if(count($user_info)) {
					$user_info['provider']="stackexchange";
					$this->handleCustomer($user_info);
                }else{
					$coreSession->addError($this->__('Login failed as you have not granted access.'));
					$customerHelper->setJsRedirect(Mage::getBaseUrl());
                }
			}
		}
	}
}