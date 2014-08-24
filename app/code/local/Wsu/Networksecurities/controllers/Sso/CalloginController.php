<?php
class Wsu_Networksecurities_Sso_CalloginController extends Mage_Core_Controller_Front_Action{
	
	/**
	* getToken and call profile user Clavid
	**/
    public function loginAction($name_blog) {
		
		$cal = Mage::getModel('wsu_networksecurities/sso_callogin')->newCal();       
		$userId = $cal->mode;        
		$coreSession = Mage::getSingleton('core/session');
		if(!$userId) {
            $cal_session = Mage::getModel('wsu_networksecurities/sso_callogin')->setcalIdlogin($aol, $name_blog);
            $url = $cal_session->authUrl();
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}else{ if (!$cal->validate()) {                
               $coreSession->addError('Login failed as you have not granted access.');			
               Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
            }else{ $user_info = $cal->getAttributes();                 
                if(count($user_info)) {
                    $frist_name = $user_info['namePerson/first'];
                    $last_name = $user_info['namePerson/last'];
                    $email = $user_info['contact/email'];
                    if(!$frist_name) {
                        if($user_info['namePerson/friendly']) {
                        $frist_name = $user_info['namePerson/friendly'] ;   
                        }else{ $email = explode("@", $email);
                            $frist_name = $email['0'];
                        }                   
                    }

                    if(!$last_name) {
                        $last_name = '_cal';
                    }
					
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add
					
                    $data = array('firstname'=>$frist_name, 'lastname'=>$last_name, 'email'=>$user_info['contact/email']);
                    $customer = Mage::helper('wsu_networksecurities/customer')->getCustomerByEmail($data['email'],$website_id );//add edition
                    if(!$customer || !$customer->getId()) {
						//Login multisite
						$customer = Mage::helper('wsu_networksecurities/customer')->createCustomerMultiWebsite($data, $website_id, $store_id );
						if (Mage::getStoreConfig('wsu_networksecurities/callogin/is_send_password_to_customer')) {
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
					Mage::helper('wsu_networksecurities/customer')->setJsRedirect($this->_loginPostRedirect());
                }else{ 
					$coreSession->addError($this->__('Login failed as you have not granted access.'));
					Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
                }
            }           
        }
    }
    
    public function setBlockAction() {             
        /*$template =  $this->getLayout()->createBlock('sociallogin/callogin')
                ->setTemplate('sociallogin/au_cal.phtml')->toHtml();
        echo $template;*/
		$this->loadLayout();
		$this->renderLayout();
    }
    
    public function setClaivdNameAction() {
        $data = $this->getRequest()->getPost();
        if($data) {
            $name = $data['name'];
            $url = Mage::getModel('wsu_networksecurities/sso_callogin')->getCalLoginUrl($name);
            $this->_redirectUrl($url);
        }else{ 
			Mage::getSingleton('core/session')->addError('Please enter Blog name!');	
            Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
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