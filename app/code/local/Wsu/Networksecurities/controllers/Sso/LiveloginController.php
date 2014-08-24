<?php
class Wsu_Networksecurities_Sso_LiveloginController extends Mage_Core_Controller_Front_Action{

	public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$isAuth = $this->getRequest()->getParam('auth');
        $code = $this->getRequest()->getParam('code');
        $live = Mage::getModel('wsu_networksecurities/sso_livelogin')->newLive();        
		try{
			$json = $live->authenticate($code);
			$user = $live->get("me", $live->param);	
		}catch(Exception $e) {
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');
			$customerHelper->setJsRedirect(Mage::getBaseUrl());
		}		
        $first_name = $user->first_name;
		$last_name = $user->last_name;
		$email = $user->emails->account;	
		//get website_id and sote_id of each stores
		$store_id = Mage::app()->getStore()->getStoreId();//add
		$website_id = Mage::app()->getStore()->getWebsiteId();//add		
		
		if ($isAuth) {
			$data =  array('firstname'=>$first_name, 'lastname'=>$last_name, 'email'=>$email);		
			$customer = $customerHelper->getCustomerByEmail($data['email'], $website_id);//add edtition
			if(!$customer || !$customer->getId()) {
				//Login multisite
				$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
				if (Mage::getStoreConfig('wsu_networksecurities/livelogin/is_send_password_to_customer')) {
					$customer->sendPasswordReminderEmail();
				}
			}
				// fix confirmation
			if ($customer->getConfirmation()) {
				try {
					$customer->setConfirmation(null);
					$customer->save();
				}catch (Exception $e) {
				}
	  		}
			Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			$customerHelper->setJsRedirect($customerHelper->_loginPostRedirect());
    	}
	}          
}