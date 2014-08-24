<?php
class Wsu_Networksecurities_Sso_TwloginController extends Mage_Core_Controller_Front_Action{
	// url to login
	
    public function loginAction() {
		
		if (!$this->getAuthorizedToken()) {
			$token = $this->getAuthorization();
		}else{ $token = $this->getAuthorizedToken();
		}
		
        return $token;
    }
	
	//url after authorize
	public function userAction() {
		$otwitter = Mage::getModel('wsu_networksecurities/sso_twlogin');
		$requestToken = Mage::getSingleton('core/session')->getRequestToken();
		
		$oauth_data = array(
                'oauth_token' => $this->getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->getRequest()->getParam('oauth_verifier')
         );
		// fixed by Hai Ta 
		try{
			 $token = $otwitter->getAccessToken($oauth_data, unserialize($requestToken));
		}catch(Exception $e) {
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');			
			Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
		}
       	//end fixed	
		$params = array(
			'consumerKey'=> Mage::helper('wsu_networksecurities')->getTwConsumerKey(), 
			'consumerSecret'=>Mage::helper('wsu_networksecurities')->getTwConsumerSecret(), 
			'accessToken'=>$token,
		);
		// $twitter = new Zend_Service_Twitter($params);
		// $twitter = new Wsu_Networksecurities_Sso_Model_Twitter($params);
		// $response = $twitter->userShow($token->user_id);
		// $twitterId = (string)$response->id;// get twitter account ID		
		$twitterId = $token->user_id;// get twitter account ID				
		$customerId = $this->getCustomerId($twitterId);
		
		if($customerId) { //login
			$customer = Mage::getModel('customer/customer')->load($customerId);
				// fix confirmation
			if ($customer->getConfirmation()) {
				try {
					$customer->setConfirmation(null);
					$customer->save();
				}catch (Exception $e) {
				}
	  		}
			Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
			Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::helper('wsu_networksecurities/customer')->_loginPostRedirect());
			
		}else{	// redirect to login page
			$name = (string)$token->screen_name;		
			$email = $name . '@twitter.com';
			$user['firstname'] = $name;
			$user['lastname'] = $name;			
			$user['email'] = $email;
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();
			$website_id = Mage::app()->getStore()->getWebsiteId();
			$customer = Mage::helper('wsu_networksecurities/customer')->getCustomerByEmail($user['email'], $website_id);//add edtition	
			if(!$customer || !$customer->getId()) {
				//Login multisite
				$customer = Mage::helper('wsu_networksecurities/customer')->createCustomerMultiWebsite($user, $website_id, $store_id );
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
			$this->setAuthorCustomer($twitterId, $customer->getId());	
			Mage::getSingleton('core/session')->setCustomerIdSocialLogin($twitterId);						
			if (Mage::getStoreConfig('wsu_networksecurities/mplogin/is_send_password_to_customer')) {
				$customer->sendPasswordReminderEmail();
			}			
			$nextUrl = Mage::helper('wsu_networksecurities')->getEditUrl();	
			Mage::getSingleton('core/session')->addNotice('Please enter your contact detail.');			
			$this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')
				->setBody("<script>window.close();window.opener.location = '$nextUrl';</script>");
		}
    }
	
	//get customer id from twitter account if user connected
	public function getCustomerId($twitterId) {
		$user = Mage::getModel('wsu_networksecurities/sso_customer')->getCollection()
						->addFieldToFilter('twitter_id', $twitterId)
						->getFirstItem();
		if($user){
			return $user->getCustomerId();
		}else{
			return NULL;
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
        $otwitter = Mage::getModel('wsu_networksecurities/sso_twlogin');		
        /* @var $otwitter Twitter_Model_Consumer */
        $otwitter->setCallbackUrl(Mage::getUrl('sociallogin/twlogin/user',array('_secure'=>true)));        
        if (!is_null($this->getRequest()->getParam('oauth_token')) && !is_null($this->getRequest()->getParam('oauth_verifier'))) {
            $oauth_data = array(
                'oauth_token' => $this->_getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->_getRequest()->getParam('oauth_verifier')
            );
            $token = $otwitter->getAccessToken($oauth_data, unserialize(Mage::getSingleton('core/session')->getRequestToken()));
            Mage::getSingleton('core/session')->setAccessToken(serialize($token));
            $otwitter->redirect();
        }else{ $token = $otwitter->getRequestToken();
            Mage::getSingleton('core/session')->setRequestToken(serialize($token));
            $otwitter->redirect();
        }
        return $token;
    }	
	
	/**
	* input: 
	*	@mpId
	*	@customerid	
	**/
	public function setAuthorCustomer($twId, $customerId) {
		$mod = Mage::getModel('wsu_networksecurities/sso_customer');
		$mod->setData('twitter_id', $twId);		
		$mod->setData('customer_id', $customerId);		
		$mod->save();		
		return ;
	}
	
	/**
	* return @collectin in model customer
	**/
	public function getCustomer ($id) {
		$collection = Mage::getModel('customer/customer')->load($id);
		return $collection;
	}
}