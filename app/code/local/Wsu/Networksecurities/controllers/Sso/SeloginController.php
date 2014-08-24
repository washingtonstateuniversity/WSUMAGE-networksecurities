<?php

class Wsu_Networksecurities_Sso_SeloginController extends Mage_Core_Controller_Front_Action{
	
	
    public function loginAction() {
		
		$se = Mage::getModel('wsu_networksecurities/sso_selogin')->newSe(); 	
		$userId = $se->mode;
		$coreSession = Mage::getSingleton('core/session');	
		if(!$userId) {
			$se_session = Mage::getModel('wsu_networksecurities/sso_selogin')->setSeIdlogin($se);
			$url = $se_session->authUrl();
			$this->_redirectUrl($url);
		}else{ if (!$se->validate()) {
				$se_session = Mage::getModel('wsu_networksecurities/sso_selogin')->setSeIdlogin($se);
				$url = $se_session->authUrl();
				$this->_redirectUrl($url);
			}else{ $user_info = $se->getAttributes();   
                if(count($user_info)) {
                    $frist_name = isset($user_info['namePerson/first'])?$user_info['namePerson/first']:"";
                    $last_name = isset($user_info['namePerson/last'])?$user_info['namePerson/last']:"";
                    $email = $user_info['contact/email'];
                    if(!$frist_name || !$last_name) {
                        if(isset($user_info['namePerson/friendly'])) {
							$frist_name = $user_info['namePerson/friendly'] ; 
							$last_name = $user_info['namePerson/friendly'];
                        }else{ $email = explode("@", $email);
                            $frist_name = $email['0'];
							$last_name  = $email['0'];
                        }                   
                    }
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add
					
                    $data = array('firstname'=>$frist_name, 'lastname'=>$last_name, 'email'=>$user_info['contact/email']);
                    $customer = Mage::helper('wsu_networksecurities/customer')->getCustomerByEmail($data['email'], $website_id);
                    if(!$customer || !$customer->getId()) {
						//Login multisite
						$customer = Mage::helper('wsu_networksecurities/customer')->createCustomerMultiWebsite($data, $website_id, $store_id );
						if (Mage::getStoreConfig('wsu_networksecurities/selogin/is_send_password_to_customer')) {
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
					$this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')
						->setBody("<script type=\"text/javascript\">try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e) {window.opener.location.reload(true);} window.close();</script>");
                }else{ $coreSession->addError($this->__('Login failed as you have not granted access.'));
					$this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')
						->setBody("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e) {window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
                }
			}
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
}