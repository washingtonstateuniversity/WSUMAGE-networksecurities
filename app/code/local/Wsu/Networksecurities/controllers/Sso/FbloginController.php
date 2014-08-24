<?php
class Wsu_Networksecurities_Sso_FbloginController extends Mage_Core_Controller_Front_Action{

    public function loginAction() {            
		
		$isAuth = $this->getRequest()->getParam('auth');
		$facebook = Mage::getModel('wsu_networksecurities/sso_fblogin')->newFacebook();
		$userId = $facebook->getUser();
		
		if($isAuth && !$userId && $this->getRequest()->getParam('error_reason') == 'user_denied') {
			echo("<script>window.close()</script>");
		}elseif ($isAuth && !$userId) {
			$loginUrl = $facebook->getLoginUrl(array('scope' => 'email'));
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
			exit;
		}
 		$user = Mage::getModel('wsu_networksecurities/sso_fblogin')->getFbUser();
 
		if ($isAuth && $user) {
			$store_id = Mage::app()->getStore()->getStoreId();//add
			$website_id = Mage::app()->getStore()->getWebsiteId();//add
			$data =  array('firstname'=>$user['first_name'], 'lastname'=>$user['last_name'], 'email'=>$user['email']);
			if($data['email']) {
				$customer = Mage::helper('wsu_networksecurities/customer')->getCustomerByEmail($data['email'],$website_id );//add edition
				if(!$customer || !$customer->getId()) {
					//Login multisite
					$customer = Mage::helper('wsu_networksecurities/customer')->createCustomerMultiWebsite($data, $website_id, $store_id );
					if(Mage::getStoreConfig('wsu_networksecurities/fblogin/is_send_password_to_customer')) {
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
				Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::helper('wsu_networksecurities/customer')->_loginPostRedirect()); 
			}else{
				Mage::getSingleton('core/session')->addError('You provided a email invalid!');			
				Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
			}
		}
	}
}