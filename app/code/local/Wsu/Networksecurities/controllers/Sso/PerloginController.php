<?php

class Wsu_Networksecurities_Sso_PerloginController extends Mage_Core_Controller_Front_Action{
	
	
    public function loginAction() {
		// url de xac nhan
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
		$status=Mage::helper('wsu_networksecurities/customer')->getPerResultStatus($result);
		if($status=='okay') {
		
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();
			$website_id = Mage::app()->getStore()->getWebsiteId();
			
			$email= Mage::helper('wsu_networksecurities/customer')->getPerEmail($result);
			$name=explode("@", $email);
			$data =  array('firstname'=>$name[0], 'lastname'=>$name[0], 'email'=>$email);
			$customer = Mage::helper('wsu_networksecurities/customer')->getCustomerByEmail($email, $website_id);
			if(!$customer || !$customer->getId()) {
				//Login multisite
				$customer = Mage::helper('wsu_networksecurities/customer')->createCustomerMultiWebsite($data, $website_id, $store_id );
				if(Mage::getStoreConfig('wsu_networksecurities/perlogin/is_send_password_to_customer')) {
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
			//die("<script type=\"text/javascript\">try{window.opener.location.href=\"".$this->_loginPostRedirect()."\";}catch(e) {window.opener.location.reload(true);} window.close();</script>");
			$this->_redirectUrl($this->_loginPostRedirect());
		}else{ //Mage::getSingleton('sociallogin')->addError('Sorry! You can not login');
			// echo "----------------------------------";
			Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access.'));
			$this->_redirect();
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
