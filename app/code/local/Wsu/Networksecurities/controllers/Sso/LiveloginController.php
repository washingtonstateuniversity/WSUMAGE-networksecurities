<?php
class Wsu_Networksecurities_Sso_LiveloginController extends Mage_Core_Controller_Front_Action{

    public function loginAction() {  
		$isAuth = $this->getRequest()->getParam('auth');
        $code = $this->getRequest()->getParam('code');
        $live = Mage::getModel('wsu_networksecurities/sso_livelogin')->newLive();        
		try{
			$json = $live->authenticate($code);
			$user = $live->get("me", $live->param);	
		}catch(Exception $e) {
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');
			Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
		}		
        $first_name = $user->first_name;
		$last_name = $user->last_name;
		$email = $user->emails->account;	
		//get website_id and sote_id of each stores
		$store_id = Mage::app()->getStore()->getStoreId();//add
		$website_id = Mage::app()->getStore()->getWebsiteId();//add		
		
		if ($isAuth) {
			$data =  array('firstname'=>$first_name, 'lastname'=>$last_name, 'email'=>$email);		
			$customer = Mage::helper('wsu_networksecurities/customer')->getCustomerByEmail($data['email'], $website_id);//add edtition
			if(!$customer || !$customer->getId()) {
				//Login multisite
				$customer = Mage::helper('wsu_networksecurities/customer')->createCustomerMultiWebsite($data, $website_id, $store_id );
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
			Mage::helper('wsu_networksecurities/customer')->setJsRedirect($this->_loginPostRedirect());
    	}
	}
	protected function _loginPostRedirect() {
        $session = Mage::getSingleton('customer/session');

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }else{ 
			if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        return $session->getBeforeAuthUrl(true);
    }           
}