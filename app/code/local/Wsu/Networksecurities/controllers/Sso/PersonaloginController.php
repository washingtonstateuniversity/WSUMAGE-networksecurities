<?php

class Wsu_Networksecurities_Sso_PersonaloginController extends Mage_Core_Controller_Front_Action{
	
    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$url = 'https://verifier.login.persona.org/verify';
		$assert=$this->getRequest()->getParam('assertion');// lay ma xac nhan	
		//Url+port
		//$audience = ($_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
		$params = 'assertion=' . urlencode($assert) . '&audience=' .
				   urlencode(Mage::getUrl());
		//gui xac nhan
		$ch = curl_init();
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_POST => 2,
			CURLOPT_POSTFIELDS => $params
		);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		curl_close($ch);
		$status = $customerHelper->getPerResultStatus($result);
		if($status=='okay') {
			$user_info['email']= Mage::helper('wsu_networksecurities/customer')->getPerEmail($result);
			$user_info['provider']="persona";
			$this->handleCustomer($user_info);
		}else{
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');			
			$customerHelper->setJsRedirect(Mage::getBaseUrl());
		}
    }
	
	
	public function makeCustomerData($user_info) {
		$data = array();

		$email = $user_info['email'];
		$name=explode("@", $email);
		$frist_name = $name[0];
		$last_name = $name[0];
		
		$data['provider']=$user_info['provider'];
		$data['email']=$email;
		$data['firstname']=$frist_name;
		$data['lastname']=$last_name;

		return $data;
	}	
	
	
}
