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
		
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();
			$website_id = Mage::app()->getStore()->getWebsiteId();
			
			$email= Mage::helper('wsu_networksecurities/customer')->getPerEmail($result);
			$name=explode("@", $email);
			$data =  array('firstname'=>$name[0], 'lastname'=>$name[0], 'email'=>$email);
			$customer = $customerHelper->getCustomerByEmail($email, $website_id);
			if(!$customer || !$customer->getId()) {
				//Login multisite
				$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
				if(Mage::getStoreConfig('wsu_networksecurities/personalogin/is_send_password_to_customer')) {
					$customer->sendPasswordReminderEmail();
				}
				if ($customer->getConfirmation()) {
					try {
						$customer->setConfirmation(null);
						$customer->save();
					}catch (Exception $e) {
						Mage::getSingleton('core/session')->addError(Mage::helper('wsu_networksecurities')->__('Error'));
					}
				}
			}
			
			Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			/*die("<script type=\"text/javascript\">try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e) {window.opener.location.reload(true);} window.close();</script>");*/
			//$customerHelper->setJsRedirect($customerHelper->_loginPostRedirect());
			$this->_redirectUrl($this->_loginPostRedirect());
		}else{
			Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access.'));
			$this->_redirect();
			//Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
		}
    }
}
