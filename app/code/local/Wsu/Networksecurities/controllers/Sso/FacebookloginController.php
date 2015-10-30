<?php
class Wsu_Networksecurities_Sso_FacebookloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {

    public function loginAction() {            
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$isAuth = $this->getRequest()->getParam('auth');
		$facebook = Mage::getModel('wsu_networksecurities/sso_facebooklogin')->getProvider();
		$userId = $facebook->getUser();

		if($isAuth && !$userId && $this->getRequest()->getParam('error_reason') == 'user_denied') {
			echo("<script>window.close()</script>");
		}elseif (($isAuth && !$userId) || $userId==0) {
			$loginUrl = Mage::getModel('wsu_networksecurities/sso_facebooklogin')->getLoginUrl();
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>"; 
 			exit; 
		}
 		$user_info = Mage::getModel('wsu_networksecurities/sso_facebooklogin')->getUser();
		if ($user_info && isset($user_info['id'])) {
			$user_info['provider']="facebook";
			$this->handleCustomer($user_info);
		}else{ 
			Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access. Try login out of facebook, if you beleive this is an error.  For more help see %s','<a href="" target="_blank">this</a>'));
			$customerHelper->setJsRedirect(Mage::getBaseUrl());
		}

	}
	public function makeCustomerData($user_info) {
		$data = array();

		$data['provider']=$user_info['provider'];
		$data['email']=$user_info['email'];
		
		$first_name=null;
		$lastname=null;
		if(isset($user_info['name'])){
			$name_parts = explode(' ',$user_info['name']);
			$first_name=$name_parts[0];
			$set = count($name_parts);
			$lastname= ($set>1 ? $name_parts[($set-1)] : '');
		}else{
			$first_name=$user_info['first_name'];
			$lastname=$user_info['last_name'];
		}
		$data['firstname']=$first_name;
		$data['lastname']=$lastname;
		
		if(isset($user_info['gender'])){
			$data['gender'] = $user_info['gender']=="male" ? '1' : '2';
		}

		return $data;
	}
	//on add user match in getCustomerAltSSo (check first name last name) 
	//>> if (so but not this sso) ask user if they have a __(machted)__ sso 
	//>> if use agrees then start login on that sso and then run add sso to account actions 
}