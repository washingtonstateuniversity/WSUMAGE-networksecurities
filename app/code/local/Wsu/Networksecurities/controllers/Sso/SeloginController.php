<?php

class Wsu_Networksecurities_Sso_SeloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {
	
    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$se = Mage::getModel('wsu_networksecurities/sso_selogin')->newSe(); 	
		$userId = $se->mode;
		$coreSession = Mage::getSingleton('core/session');	
		if(!$userId) {
			$se_session = Mage::getModel('wsu_networksecurities/sso_selogin')->setSeIdlogin($se);
			$url = $se_session->authUrl();
			$this->_redirectUrl($url);
		}else{ 
			if (!$se->validate()) {
				$se_session = Mage::getModel('wsu_networksecurities/sso_selogin')->setSeIdlogin($se);
				$url = $se_session->authUrl();
				$this->_redirectUrl($url);
			}else{ 
				$user_info = $se->getAttributes();   
                if(count($user_info)) {
					$data = $this->makeCustomerData($user_info);
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add

                    $customer = $customerHelper->getCustomerByEmail($data['email'], $website_id);
					
                    if(!$customer || !$customer->getId()) {
						$customer = $customer->getCustomerAltSSo($customer,$data);
						if(!$customer || !$customer->getId()) {
							$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
						}
                    }else{
						$_customer = $customer->getCustomerAltSSo($customer,$data);
						if(!$_customer || !$_customer->getId()) {
							$customer = $customer->addSsoMap($customer,$data);
						}
					}
                    Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
					$customerHelper->setJsRedirect($customerHelper->_loginPostRedirect());
                }else{
					$coreSession->addError($this->__('Login failed as you have not granted access.'));
					$customerHelper->setJsRedirect(Mage::getBaseUrl());
                }
			}
		}
	}
}