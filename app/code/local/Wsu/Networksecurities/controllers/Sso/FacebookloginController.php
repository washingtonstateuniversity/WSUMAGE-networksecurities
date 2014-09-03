<?php
class Wsu_Networksecurities_Sso_FacebookloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {

    public function loginAction() {            
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$isAuth = $this->getRequest()->getParam('auth');
		$facebook = Mage::getModel('wsu_networksecurities/sso_facebooklogin')->newProvider();
		$userId = $facebook->getUser();
		
		if($isAuth && !$userId && $this->getRequest()->getParam('error_reason') == 'user_denied') {
			echo("<script>window.close()</script>");
		}elseif ($isAuth && !$userId) {
			$loginUrl = $facebook->getLoginUrl(array('scope' => 'email'));
			$this->_redirectUrl($loginUrl);
		}
 		$user_info = Mage::getModel('wsu_networksecurities/sso_facebooklogin')->getUser();
		if ($isAuth && $user_info) {
			$user_info['provider']="facebook";
			$this->handleCustomer($user_info);
		}else{ 
			Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access.'));
			$customerHelper->setJsRedirect(Mage::getBaseUrl());
		}

	}
	public function makeCustomerData($user_info) {
		$data = array();

		$data['provider']=$user_info['provider'];
		$data['email']=$user_info['email'];
		$data['firstname']=$user_info['first_name'];
		$data['lastname']=$user_info['last_name'];
		
		$gender = $user_info['gender'];
		if(isset($gender)){
			$data['gender'] = $gender=="male" ? '1' : '2';
		}

		return $data;
	}
	//on add user match in getCustomerAltSSo (check first name last name) 
	//>> if (so but not this sso) ask user if they have a __(machted)__ sso 
	//>> if use agrees then start login on that sso and then run add sso to account actions 
}