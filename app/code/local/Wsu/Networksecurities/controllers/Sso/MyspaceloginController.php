<?php
class Wsu_Networksecurities_Sso_MyspaceloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {


    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer'); 
		$requestToken = Mage::getSingleton('core/session')->getRequestToken();
		
		$mp = Mage::getModel('wsu_networksecurities/sso_myspacelogin')->getProvider($requestToken);
		$oauth_token = $this->getRequest()->getParam('oauth_token');
        $oauth_verifier = $this->getRequest()->getParam('oauth_verifier');		
		$accessToken = $mp->accessToken($oauth_verifier, urldecode ($oauth_token));
		$userId = $mp->get('http://api.myspace.com/v1/user.json')->userId;		
		$data = $mp->get( 'http://api.myspace.com/v1/users/' . $userId . '/profile.json' );		
		if ( ! is_object( $data ) ) {			
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');			
			$customerHelper->setJsRedirect(Mage::getBaseUrl());
		}
		
		$customerId = $this->getCustomerId($userId);
		
		if (!$customerId) {			
			$name = $data->basicprofile->name;
			$email = $userId . '@myspace.com';
			$user['firstname'] = $name;
			$user['lastname'] = $name;
			$user['email'] = $email;
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();
			$website_id = Mage::app()->getStore()->getWebsiteId();
			$customer = $customerHelper->getCustomerByEmail($user['email'], $website_id);//add edtition
			if(!$customer || !$customer->getId()) {
				//Login multisite
				$customer = $customerHelper->createCustomerMultiWebsite($user, $website_id, $store_id );
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
			Mage::getSingleton('core/session')->setCustomerIdSocialLogin($userId);			
			$this->setAuthorCustomer($userId, $customer->getId());				
			if (Mage::getStoreConfig('wsu_networksecurities/myspacelogin/is_send_password_to_customer')) {
				$customer->sendPasswordReminderEmail();
			} 
			$nextUrl = $customerHelper->getEditUrl();
			Mage::getSingleton('core/session')->addNotice('Please enter your contact detail.');
			
			$this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')
					->setBody("<script>window.close();window.opener.location = '$nextUrl';</script>");
		}else{ 
			$customer = $this->getCustomer($customerId);	
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
		}		
    }
	
	/**
	* return customer_id in table authorlogin
	**/
	public function getCustomerId($mpId) {
		$user = Mage::getModel('wsu_networksecurities/sso_authorlogin')->getCollection()
						->addFieldToFilter('author_id', $mpId)
						->getFirstItem();
		if($user){
			return $user->getCustomerId();
		}else{
			return NULL;
		}
	}
	
	/**
	* input: 
	*	@mpId
	*	@customerid	
	**/
	public function setAuthorCustomer($mpId, $customerId) {
		$mod = Mage::getModel('wsu_networksecurities/sso_authorlogin');
		$mod->setData('author_id', $mpId);		
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