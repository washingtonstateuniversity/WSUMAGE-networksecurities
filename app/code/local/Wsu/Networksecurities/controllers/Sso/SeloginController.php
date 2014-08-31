<?php

class Wsu_Networksecurities_Sso_SeloginController extends Mage_Core_Controller_Front_Action{
	
	
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
					$data = array();
					 
                    $frist_name = isset($user_info['namePerson/first'])?$user_info['namePerson/first']:"";
                    $last_name = isset($user_info['namePerson/last'])?$user_info['namePerson/last']:"";
                    $email = $user_info['contact/email'];
					
                    if(!$frist_name || !$last_name) {
                        if(isset($user_info['namePerson/friendly'])) {
							$frist_name = $user_info['namePerson/friendly'] ; 
							$last_name = $user_info['namePerson/friendly'];
							$data['username']=$user_info['namePerson/friendly'];
                        }else{ $emailpart = explode("@", $email);
                            $frist_name = $emailpart['0'];
							$last_name  = $emailpart['0'];
							$data['username']=$email;
                        }                   
                    }
					
					$data['provider']="stackexchange";
					$data['email']=$email;
					$data['firstname']=$frist_name;
					$data['lastname']=$last_name;
					
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add

                    $customer = $customerHelper->getCustomerByEmail($data['email'], $website_id);
					
                    if(!$customer || !$customer->getId()) {
						$customer = $customer->getCustomerAltSSo($customer,$data);
						if(!$customer || !$customer->getId()) {
							$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
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