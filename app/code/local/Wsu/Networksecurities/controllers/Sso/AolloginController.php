<?php
class Wsu_Networksecurities_Sso_AolloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {
    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$aol = Mage::getModel('wsu_networksecurities/sso_aollogin')->newProvider();       
		$userId = $aol->mode;        

		if (!$userId || !$aol->validate()) {                
			$aol_session = Mage::getModel('wsu_networksecurities/sso_aollogin')->setIdlogin($aol);
			$url = $aol_session->authUrl();
			$this->_redirectUrl($url);
		}else{
			$user_info = $aol->getAttributes();                 
			if(count($user_info)) {
				$user_info['provider']="aol";
				$this->handleCustomer($user_info);
			}else{ 
				Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access.'));
				$customerHelper->setJsRedirect(Mage::getBaseUrl());
			}
		}
    }
}