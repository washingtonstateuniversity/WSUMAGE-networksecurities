<?php
class Wsu_Networksecurities_Sso_OrangeloginController extends Mage_Core_Controller_Front_Action{

	/**
	* getToken and call profile user Orange
	**/
    public function loginAction() {     
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$org = Mage::getModel('wsu_networksecurities/sso_orangelogin')->newProvider();            
		$coreSession = Mage::getSingleton('core/session');                      
		$user_info = $org->data;                 
		if(count($user_info)) {
			$user_info['provider']="myopenid";
			$this->handleCustomer($user_info);
		}else{ 
			$coreSession->addError('Login failed as you have not granted access.');			
			$customerHelper->setJsRedirect(Mage::getBaseUrl());
		}           
    }
	
	public function makeCustomerData($user_info) {
		$data = array();

		$frist_name = $user_info['openid_sreg_nickname'];
		$last_name = $user_info['openid_sreg_nickname'];
		$email = $user_info['openid_sreg_email'];
		
		$data['provider']=$user_info['provider'];
		$data['email']=$email;
		$data['firstname']=$frist_name;
		$data['lastname']=$last_name;
		$data['authorId']=$authorId;

		return $data;
	}	
}