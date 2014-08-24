<?php
class Wsu_Networksecurities_Sso_MploginController extends Mage_Core_Controller_Front_Action{
	/**
	* getToken and call profile user myspace
	**/
    public function loginAction() {
		
		$requestToken = Mage::getSingleton('core/session')->getRequestToken();
		
		$mp = Mage::getModel('wsu_networksecurities/sso_mplogin')->newMp($requestToken);
		$oauth_token = $this->getRequest()->getParam('oauth_token');
        $oauth_verifier = $this->getRequest()->getParam('oauth_verifier');		
		$accessToken = $mp->accessToken($oauth_verifier, urldecode ($oauth_token));
		$userId = $mp->get('http://api.myspace.com/v1/user.json')->userId;		
		$data = $mp->get( 'http://api.myspace.com/v1/users/' . $userId . '/profile.json' );		
		if ( ! is_object( $data ) ) {			
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');			
			Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
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
			Mage::getSingleton('core/session')->setCustomerIdSocialLogin($userId);			
			$this->setAuthorCustomer($userId, $customer->getId());				
			if (Mage::getStoreConfig('wsu_networksecurities/mplogin/is_send_password_to_customer')) {
				$customer->sendPasswordReminderEmail();
			} 
			$nextUrl = Mage::helper('wsu_networksecurities')->getEditUrl();
			Mage::getSingleton('core/session')->addNotice('Please enter your contact detail.');
			
			$this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')
					->setBody("<script>window.close();window.opener.location = '$nextUrl';</script>");
		}else{ $customer = $this->getCustomer($customerId);	
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
	
	/**
	* return customer_id in table authorlogin
	**/
	public function getCustomerId($mpId) {
		$user = Mage::getModel('wsu_networksecurities/sso_authorlogin')->getCollection()
						->addFieldToFilter('author_id', $mpId)
						->getFirstItem();
		if($user)
			return $user->getCustomerId();
		else
			return NULL;
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