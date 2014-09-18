<?php

class Wsu_Networksecurities_Sso_StackexchangeloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {
	
    public function loginAction() {
		$se = Mage::getModel('wsu_networksecurities/sso_stackexchangelogin')->getProvider(); 	
		$userId = $se->mode;
		if(!$userId) {
			$se_session = Mage::getModel('wsu_networksecurities/sso_stackexchangelogin')->setIdlogin($se);
			$url = $se_session->authUrl();
			$this->_redirectUrl($url);
		}else{ 
			if (!$se->validate()) {
				$se_session = Mage::getModel('wsu_networksecurities/sso_stackexchangelogin')->setIdlogin($se);
				$url = $se_session->authUrl();
				$this->_redirectUrl($url);
			}else{ 
				$user_info = $se->getAttributes();
                if(count($user_info)) {
					$user_info['provider']="stackexchange";
					$this->handleCustomer($user_info);
                }else{
					Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access.'));
					Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
                }
			}
		}
	}
}