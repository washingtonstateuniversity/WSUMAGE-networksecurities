<?php
class Wsu_Networksecurities_Sso_LiveloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {

	public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$isAuth = $this->getRequest()->getParam('auth');
        $code = $this->getRequest()->getParam('code');
        $live = Mage::getModel('wsu_networksecurities/sso_livelogin')->getProvider();        
		try{
			$json = $live->authenticate($code);
			$user = $live->get("me", $live->param);
			if ($isAuth) {
				$user_info['provider']="live";
				$user_info['user']=$user;
				$this->handleCustomer($user_info);
			}
		}catch(Exception $e) {
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');
			$customerHelper->setJsRedirect(Mage::getBaseUrl());
		}
	}
	
	public function makeCustomerData($user_info) {
		$data = array();

        $first_name = $user_info['user']->first_name;
		$last_name = $user_info['user']->last_name;
		$email = $user_info['user']->emails->account;
		
		$data['provider']=$user_info['provider'];
		$data['email']=$email;
		$data['firstname']=$frist_name;
		$data['lastname']=$last_name;

		return $data;
	}
	
}