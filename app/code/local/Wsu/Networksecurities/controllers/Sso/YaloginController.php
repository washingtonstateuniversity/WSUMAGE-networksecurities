<?php

class Wsu_Networksecurities_Sso_YaloginController extends Mage_Core_Controller_Front_Action{
	
	// url to login
    public function loginAction() {
		
		$yalogin = Mage::getModel('wsu_networksecurities/sso_yalogin');
		$hasSession = $yalogin->hasSession();
		if($hasSession == FALSE) {
			$authUrl = $yalogin->getAuthUrl();			
			$this->_redirectUrl($authUrl);
		}else{
			$session = $yalogin->getSession();
			$userSession = $session->getSessionedUser();
			$profile = $userSession->loadProfile();
			$emails = $profile->emails;
			$user = array();
			foreach($emails as $email) {
				if($email->primary == 1)
					$user['email'] = $email->handle;
			}
			$user['firstname'] = $profile->givenName;
			$user['lastname'] = $profile->familyName;
			
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();
			$website_id = Mage::app()->getStore()->getWebsiteId();
			
			$customer = Mage::helper('wsu_networksecurities/customer')->getCustomerByEmail($user['email'], $website_id);
			if(!$customer || !$customer->getId()) {
				//Login multisite
				$customer = Mage::helper('wsu_networksecurities/customer')->createCustomerMultiWebsite($user, $website_id, $store_id );
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
				}
	  		}
			Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			Mage::helper('wsu_networksecurities/customer')->setJsRedirect($this->_loginPostRedirect());
			//$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl());
		}
		
    }
	protected function _loginPostRedirect() {
        $session = Mage::getSingleton('customer/session');

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
            
        }else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }else{ if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
		
        return $session->getBeforeAuthUrl(true);
    }
	
	public function testAction() {
		
	}
}