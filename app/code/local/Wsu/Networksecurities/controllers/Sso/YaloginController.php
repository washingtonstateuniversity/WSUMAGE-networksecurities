<?php

class Wsu_Networksecurities_Sso_YaloginController extends Mage_Core_Controller_Front_Action{
	
	// url to login
    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$yalogin = Mage::getModel('wsu_networksecurities/sso_yalogin');
		$hasSession = $yalogin->hasSession();
		if($hasSession == FALSE) {
			$authUrl = $yalogin->getAuthUrl();			
			$this->_redirectUrl($authUrl);
		}else{
			$session = $yalogin->getSession();
			$userSession = $session->getSessionedUser();
			$profile = $userSession->loadProfile();
			
			var_dump($profile);die();
			
			$emails = $profile->emails;
			$user = array();
			foreach($emails as $email) {
				if($email->primary == 1){
					$user['email'] = $email->handle;
				}
			}
			$user['firstname'] = $profile->givenName;
			$user['lastname'] = $profile->familyName;
			
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();
			$website_id = Mage::app()->getStore()->getWebsiteId();
			
			$customer = $customerHelper->getCustomerByEmail($user['email'], $website_id);
			if(!$customer || !$customer->getId()) {
				//Login multisite
				$customer = $customerHelper->createCustomerMultiWebsite($user, $website_id, $store_id );
				if (Mage::getStoreConfig('wsu_networksecurities/yalogin/is_send_password_to_customer')) {
					$customer->sendPasswordReminderEmail();
				}
			}
				// fix confirmation
			if ($customer->getConfirmation()) {
				try {
					$customer->setConfirmation(null);
					$customer->save();
				}catch (Exception $e) {
					Mage::getSingleton('core/session')->addError(Mage::helper('wsu_networksecurities')->__('Error').$e->getMessage());
				}
	  		}
			Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			$customerHelper->setJsRedirect($customerHelper->_loginPostRedirect());
			//$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
		}
		
    }
}