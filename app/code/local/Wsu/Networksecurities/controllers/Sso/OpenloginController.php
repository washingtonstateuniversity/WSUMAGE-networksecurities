<?php
class Wsu_Networksecurities_Sso_OpenloginController extends Mage_Core_Controller_Front_Action{
   
   public function loginAction() {     
		$identity = $this->getRequest()->getPost('identity');
		$my = Mage::getModel('wsu_networksecurities/sso_openlogin')->newMy();  
		Mage::getSingleton('core/session')->setData('identity',$identity);		
		$userId = $my->mode;       	
		$coreSession = Mage::getSingleton('core/session');
		if(!$userId) {
			$my = Mage::getModel('wsu_networksecurities/sso_openlogin')->setOpenIdlogin($my,$identity);
			try{
				$url = $my->authUrl();
			}catch(Exception $e) {
				$coreSession->addError('Username not exacted');			
                die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e) {window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
			}
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}else{ if (!$my->validate()) {                
                $my = Mage::getModel('wsu_networksecurities/sso_openlogin')->setOpenIdlogin($my,$identity);
                try{
					$url = $my->authUrl();
				}catch(Exception $e) {
					$coreSession->addError('Username not exacted');			
					die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e) {window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
				}
                echo "<script type='text/javascript'>top.location.href = '$url';</script>";
                exit;
            }else{ //$user_info = $my->getAttributes(); 
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
					$email = $firstname."@".$userArray[1];
					$authorId = $email;
					$user['firstname'] = $firstname;
					$user['lastname'] = $lastname;
					$user['email'] = $email;
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();
					$website_id = Mage::app()->getStore()->getWebsiteId();
						$customer = Mage::helper('wsu_networksecurities/customer')->getCustomerByEmail($email, $website_id);
						if(!$customer || !$customer->getId()) {
							//Login multisite
							$customer = Mage::helper('wsu_networksecurities/customer')->createCustomerMultiWebsite($user, $website_id, $store_id );
						}
						Mage::getModel('wsu_networksecurities/sso_authorlogin')->addCustomer($authorId);
						if (Mage::getStoreConfig('wsu_networksecurities/oplogin/is_send_password_to_customer')) {
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
						$nextUrl = Mage::helper('wsu_networksecurities')->getEditUrl();						
						die("<script>window.close();window.opener.location = '$nextUrl';</script>");
			
                }else{
                   $coreSession->addError('User has not shared information so login fail!');			
                   die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e) {window.opener.location.href=\"".Mage::getBaseUrl()."\"} window.close();</script>");
                }
            }           
        }
    }
	
	/**
	* return template au_wp.phtml
	**/
    public function setBlockAction() {             
		$this->loadLayout();
		$this->renderLayout();
        /*$template =  $this->getLayout()->createBlock('sociallogin/openlogin')
                ->setTemplate('sociallogin/au_op.phtml')->toHtml();
        echo $template;*/
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