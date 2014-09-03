<?php
class Wsu_Networksecurities_Sso_FoursquareloginController extends Mage_Core_Controller_Front_Action{

	/**
	* getToken and call profile user FoursQuare
	**/
    public function loginAction() {            		
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$isAuth = $this->getRequest()->getParam('auth');
		$foursquare = Mage::getModel('wsu_networksecurities/sso_foursquarelogin')->getProvider();
		$code = $_REQUEST['code'];	
		$date = date('Y-m-d');
		$date = str_replace('-', '', $date);
		$oauth = $foursquare->GetToken($code);

		if(!$oauth) {
			echo("<script>window.close()</script>");
			return ;
		}
		$url = 'https://api.foursquare.com/v2/users/self?oauth_token='.$oauth.'&v='.$date;
		try{
			$json = $customerHelper->getResponseBody($url);
		}catch( Exception $e) {
			Mage::getSingleton('core/session')->addError('Login fail!');			
            $customerHelper->setJsRedirect(Mage::getBaseUrl());
		}		
		$string = $foursquare->getResponseFromJsonString($json);		
		$first_name = $string->user->firstName;
		$last_name = $string->user->lastName;
		$email = $string->user->contact->email;						
		if ($isAuth && $oauth) {
		
			//get website_id and sote_id of each stores
			$store_id = Mage::app()->getStore()->getStoreId();//add
			$website_id = Mage::app()->getStore()->getWebsiteId();//add
			
			$data =  array('firstname'=>$first_name, 'lastname'=>$last_name, 'email'=>$email);
			$customer = $customerHelper->getCustomerByEmail($data['email'],$website_id );//add edition
			if(!$customer || !$customer->getId()) { //if customer not exist
				//Login multisite
				$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
				if(Mage::getStoreConfig('wsu_networksecurities/foursquare_login/is_send_password_to_customer')) {
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
			}else{ //if customer exist
				$getConfirmPassword = (int)Mage::getStoreConfig('wsu_networksecurities/foursquare_login/is_customer_confirm_password');
				if($getConfirmPassword) {
					$this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')
					->setBody("<script type=\"text/javascript\">var email = '$email';window.opener.opensocialLogin();window.opener.document.getElementById('wsu_sso-sociallogin-popup-email').value = email;window.close();</script>  ");
				}else{
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
	}
}