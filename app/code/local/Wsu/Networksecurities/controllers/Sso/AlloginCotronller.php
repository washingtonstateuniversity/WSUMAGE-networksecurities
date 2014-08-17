<?php
class Wsu_Networksecurities_Sso_AlloginController extends Mage_Core_Controller_Front_Action{

	/**
	* getToken and call profile user aol
	**/
    public function loginAction() { 
		
		$aol = Mage::getModel('wsu_networksecurities/sso_allogin')->newAol();       
		$userId = $aol->mode;        
		$coreSession = Mage::getSingleton('core/session');		
		if(!$userId) {
            $aol_session = Mage::getModel('wsu_networksecurities/sso_allogin')->setAolIdlogin($aol);
            $url = $aol_session->authUrl();
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}else{ if (!$aol->validate()) {                
               $aol_session = Mage::getModel('wsu_networksecurities/sso_allogin')->setAolIdlogin($aol);
                $url = $aol_session->authUrl();
                echo "<script type='text/javascript'>top.location.href = '$url';</script>";
                exit;
            }else{ $user_info = $aol->getAttributes();                 
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
                        $last_name = '_aol';
                    }
					
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add
					
                    $data = array('firstname'=>$frist_name, 'lastname'=>$last_name, 'email'=>$user_info['contact/email']);
                    $customer = Mage::helper('wsu_networksecurities/customer')->getCustomerByEmail($data['email'],$website_id);
                    if(!$customer || !$customer->getId()) {
						//login multisite 
						$customer = Mage::helper('wsu_networksecurities/customer')->createCustomerMultiWebsite($data, $website_id, $store_id );
						if (Mage::getStoreConfig('wsu_networksecurities/aollogin/is_send_password_to_customer')) {
							$customer->sendPasswordReminderEmail();
						}
                    }
                    Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
                    die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e) {window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
                }else{ $coreSession->addError('Login failed as you have not granted access.');			
                   die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e) {window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
                }
            }           
        }
    }
	
	
	public function setBlockAction() {             
        $template =  $this->getLayout()->createBlock('sociallogin/aollogin')
                ->setTemplate('sociallogin/au_wp.phtml')->toHtml();
        echo $template;
    }
    
	
    public function setScreenNameAction() {
        $data = $this->getRequest()->getPost();		
		$name = $data['name'];
        if($name) {            
            $url = Mage::getModel('wsu_networksecurities/sso_allogin')->getAlLoginUrl($name);			
            $this->_redirectUrl($url);
        }else{ Mage::getSingleton('core/session')->addError('Please enter Blog name!');	
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e) {window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
        }
    }
}
