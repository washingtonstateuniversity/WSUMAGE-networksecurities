<?php
class Wsu_Networksecurities_Sso_LjloginController extends Mage_Core_Controller_Front_Action{
	public function loginAction() {  
		$customerHelper = Mage::helper('wsu_networksecurities/customer');    
		$identity = $this->getRequest()->getPost('identity');
		Mage::getSingleton('core/session')->setData('identity',$identity);
		$my = Mage::getModel('wsu_networksecurities/sso_ljlogin')->newMy();
		/*$my->required = array(
        'namePerson/first',
        'namePerson/last',
        'namePerson/friendly',
        'contact/email',
		'namePerson' 
        );*/
		Mage::getSingleton('core/session')->setData('identity',$identity);
		$userId = $my->mode;       	
		$coreSession = Mage::getSingleton('core/session');
		if(!$userId) {
            $my = Mage::getModel('wsu_networksecurities/sso_ljlogin')->setLjIdlogin($my,$identity);
			try{
				$url = $my->authUrl();
			}catch(Exception $e) {
				$coreSession->addError('Username not exacted');
				$customerHelper->setJsRedirect(Mage::getBaseUrl());
			}
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}else{ if (!$my->validate()) { 
               $my_session = Mage::getModel('wsu_networksecurities/sso_ljlogin')->setLjIdlogin($my,$identity);
                try{
					$url = $my->authUrl();
				}catch(Exception $e) {
					$coreSession->addError('Username not exacted');			
					$customerHelper->setJsRedirect(Mage::getBaseUrl());
				}
                echo "<script type='text/javascript'>top.location.href = '$url';</script>";
                exit;
            }else{ // $user_info = $my->getAttributes();
				$user_info = $my->data;
                if(count($user_info)) {
					$user = array();
					$identity = $user_info['openid_identity'];
					$length = strlen($identity);
					$httpLen = strlen("http://");
					$userAccount = substr($identity,$httpLen,$length-1-$httpLen);
					$userArray = explode( '.', $userAccount,2);
					$firstname = $userArray[0];
					$lastname ="";
					$email = $firtname."@".$userArray[1];
					$user['firstname'] = $firstname;
					$user['lastname'] = $lastname;
					$user['email'] = $email;
					$authorId = $email;
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add	
					$customer = $customerHelper->getCustomerByEmail($user['email'], $website_id);//add edtition
					if(!$customer || !$customer->getId()) {
						//Login multisite
						$customer = $customerHelper->createCustomerMultiWebsite($user, $website_id, $store_id );
					}
					Mage::getModel('wsu_networksecurities/sso_authorlogin')->addCustomer($authorId);
 					if (Mage::getStoreConfig('wsu_networksecurities/ljlogin/is_send_password_to_customer')) {
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
					$coreSession->addError('User has not shared information so login fail!');			
					Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
                }
            }           
        }
    }
	
	/**
	* return template au_wp.phtml
	**/
    public function setBlockAction() {             
        /*$template =  $this->getLayout()->createBlock('sociallogin/ljlogin')
                ->setTemplate('sociallogin/au_lj.phtml')->toHtml();
        echo $template;*/
		$this->loadLayout();
		$this->renderLayout();
    }
}