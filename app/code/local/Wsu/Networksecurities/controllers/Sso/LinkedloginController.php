<?php
class Wsu_Networksecurities_Sso_LinkedloginController extends Mage_Core_Controller_Front_Action{
	
	public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		if (!$this->getAuthorizedToken()) {
			try{
				$token = $this->getAuthorization();
			}catch(Exception $e) {
				Mage::getSingleton('core/session')->addError('Htpp not request.Please input api key on config again');
				$customerHelper->setJsRedirect(Mage::getBaseUrl());	
			}
		}else{
			$token = $this->getAuthorizedToken();
		}
        return $token;
    }
	
	public function userAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$linkedlogin = Mage::getModel('wsu_networksecurities/sso_linkedlogin');
		
		$oauth_data = array(
			'oauth_token' => $this->getRequest()->getParam('oauth_token'),
			'oauth_verifier' => $this->getRequest()->getParam('oauth_verifier')
         );
		 
		$requestToken = Mage::getSingleton('core/session')->getRequestToken(array('scope' =>'r_emailaddress'));
		try{
			$accessToken = $linkedlogin ->getAccessToken($oauth_data, unserialize($requestToken));
		}catch(Exception $e) {
			Mage::getSingleton('core/session')->addError('User has not shared information so login fail!');
			$customerHelper->setJsRedirect(Mage::getBaseUrl());	
		}
		$oauthOptions = $linkedlogin->getOptions();
		$options = $oauthOptions;
		$client = $accessToken->getHttpClient($options);
		$client->setUri('http://api.linkedin.com/v1/people/~');
		$client->setMethod(Zend_Http_Client::GET);
		$response = $client->request();
		$content =  $response->getBody();
		$xml = simplexml_load_string ($content);
		$user = array();
		$firstname = (string) $xml->{'first-name'};
		$lastname = (string) $xml->{'last-name'};
		$client2 = $accessToken->getHttpClient($options);
		$client2->setUri('http://api.linkedin.com/v1/people/~/email-address');
		$client2->setMethod(Zend_Http_Client::GET);
		$response2 = $client2->request();
		$content2 =  $response2->getBody();
		$xml2 = simplexml_load_string ($content2);
		$email = (string) $xml2->{0};
		if(!$email) {
			$email = $firstname.$lastname."@linkedin.com";
		}
		
		$user['firstname'] = $firstname;
		$user['lastname'] = $lastname;
		$user['email'] = $email;
		
		//get website_id and sote_id of each stores
		$store_id = Mage::app()->getStore()->getStoreId();
		$website_id = Mage::app()->getStore()->getWebsiteId();
		
		$customer = $customerHelper->getCustomerByEmail($email,$website_id);//add edition
		if(!$customer || !$customer->getId()) {
			//Login multisite
				$customer = $customerHelper->createCustomerMultiWebsite($user, $website_id, $store_id );
				if (Mage::getStoreConfig('wsu_networksecurities/linklogin/is_send_password_to_customer')) {
					$customer->sendPasswordReminderEmail();
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
		}else{
			$getConfirmPassword = (int)Mage::getStoreConfig('wsu_networksecurities/linklogin/is_customer_confirm_password');
			if($getConfirmPassword) {
				$this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')
					->setBody("<script type=\"text/javascript\">var email = '$email';window.opener.opensocialLogin();window.opener.document.getElementById('wsu_sso-sociallogin-popup-email').value = email;window.close();</script>  ");
			}else{ // fix confirmation
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

	// if exit access token
	public function getAuthorizedToken() {
        $token = false;
        if (!is_null(Mage::getSingleton('core/session')->getAccessToken())) {
            $token = unserialize(Mage::getSingleton('core/session')->getAccessToken());
        }
        return $token;
    }
     
	// if not exit access token
    public function getAuthorization() {
	 	$scope = 'r_emailaddress';
        $olinked = Mage::getModel('wsu_networksecurities/sso_linkedlogin');
        $olinked ->setCallbackUrl(Mage::getUrl('sociallogin/linkedlogin/user'));        
        if (!is_null($this->getRequest()->getParam('oauth_token')) && !is_null($this->getRequest()->getParam('oauth_verifier'))) {
            $oauth_data = array(
                'oauth_token' => $this->_getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->_getRequest()->getParam('oauth_verifier')
            );
            $token =  $olinked ->getAccessToken($oauth_data, unserialize(Mage::getSingleton('core/session')->getRequestToken()));
            Mage::getSingleton('core/session')->setAccessToken(serialize($token));
			$olinked ->redirect();
        }else{
			$token = $olinked ->getRequestToken(array('scope' => $scope));
            Mage::getSingleton('core/session')->setRequestToken(serialize($token));
            $olinked ->redirect();
        }
        return $token;
    }
}