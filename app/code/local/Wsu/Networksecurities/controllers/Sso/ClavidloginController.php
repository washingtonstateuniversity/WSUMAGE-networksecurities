<?php
class Wsu_Networksecurities_Sso_ClavidloginController extends Mage_Core_Controller_Front_Action{
	
	/**
	* getToken and call profile user Clavid
	**/
    public function loginAction($name_blog) {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$cal = Mage::getModel('wsu_networksecurities/sso_clavidlogin')->newProvider();       
		$userId = $cal->mode;        
		$coreSession = Mage::getSingleton('core/session');
		if(!$userId) {
            $cal_session = Mage::getModel('wsu_networksecurities/sso_clavidlogin')->setcalIdlogin($aol, $name_blog);
            $url = $cal_session->authUrl();
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}else{ if (!$cal->validate()) {                
               $coreSession->addError('Login failed as you have not granted access.');			
               $customerHelper->setJsRedirect(Mage::getBaseUrl());
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
                    $customer = $customerHelper->getCustomerByEmail($data['email'],$website_id );//add edition
                    if(!$customer || !$customer->getId()) {
						//Login multisite
						$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
						if (Mage::getStoreConfig('wsu_networksecurities/clavidlogin/is_send_password_to_customer')) {
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
					$customerHelper->setJsRedirect($customerHelper->_loginPostRedirect());
                }else{ 
					$coreSession->addError($this->__('Login failed as you have not granted access.'));
					$customerHelper->setJsRedirect(Mage::getBaseUrl());
                }
            }           
        }
    }
    
    public function setBlockAction() {             
        /*$template =  $this->getLayout()->createBlock('sociallogin/clavidlogin')
                ->setTemplate('sociallogin/au_cal.phtml')->toHtml();
        echo $template;*/
		$this->loadLayout();
		$this->renderLayout();
    }
    
    public function setClaivdNameAction() {
        $data = $this->getRequest()->getPost();
        if($data) {
            $name = $data['name'];
            $url = Mage::getModel('wsu_networksecurities/sso_clavidlogin')->getCalLoginUrl($name);
            $this->_redirectUrl($url);
        }else{ 
			Mage::getSingleton('core/session')->addError('Please enter Blog name!');	
            Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
        }
 
   }
}