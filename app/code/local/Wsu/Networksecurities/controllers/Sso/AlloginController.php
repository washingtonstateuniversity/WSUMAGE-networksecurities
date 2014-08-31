<?php
class Wsu_Networksecurities_Sso_AlloginController extends Mage_Core_Controller_Front_Action{

	/**
	* getToken and call profile user aol
	**/
    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$aol = Mage::getModel('wsu_networksecurities/sso_allogin')->newAol();       
		$userId = $aol->mode;        
		$coreSession = Mage::getSingleton('core/session');		
		if(!$userId) {
            $aol_session = Mage::getModel('wsu_networksecurities/sso_allogin')->setAolIdlogin($aol);
            $url = $aol_session->authUrl();
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
			exit;
		}else{
			if (!$aol->validate()) {                
               $aol_session = Mage::getModel('wsu_networksecurities/sso_allogin')->setAolIdlogin($aol);
                $url = $aol_session->authUrl();
                echo "<script type='text/javascript'>top.location.href = '$url';</script>";
                exit;
            }else{
				$user_info = $aol->getAttributes();                 
                if(count($user_info)) {
					$data = array();
					 
                    $frist_name = isset($user_info['namePerson/first'])?$user_info['namePerson/first']:"";
                    $last_name = isset($user_info['namePerson/last'])?$user_info['namePerson/last']:"";
                    $email = $user_info['contact/email'];
					
                    if(!$frist_name || !$last_name) {
                        if(isset($user_info['namePerson/friendly'])) {
							$frist_name = $user_info['namePerson/friendly'] ; 
							$last_name = $user_info['namePerson/friendly'];
							$data['username']=$user_info['namePerson/friendly'];
                        }else{ $emailpart = explode("@", $email);
                            $frist_name = $emailpart['0'];
							$last_name  = $emailpart['0'];
							$data['username']=$email;
                        }                   
                    }
					
					$data['provider']="stackexchange";
					$data['email']=$email;
					$data['firstname']=$frist_name;
					$data['lastname']=$last_name;
					
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add

                    $customer = $customerHelper->getCustomerByEmail($data['email'], $website_id);
					
                    if(!$customer || !$customer->getId()) {
						$customer = $customer->getCustomerAltSSo($customer,$data);
						if(!$customer || !$customer->getId()) {
							$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
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
       /* $template =  $this->getLayout()->createBlock('sociallogin/aollogin')
                ->setTemplate('sociallogin/au_al.phtml')->toHtml();
        echo $template;*/
		$this->loadLayout();
		$this->renderLayout();
    }
   
    public function setScreenNameAction() {
        $data = $this->getRequest()->getPost();		
		$name = $data['name'];
        if($name) {            
            $url = Mage::getModel('wsu_networksecurities/sso_allogin')->getAlLoginUrl($name);			
            $this->_redirectUrl($url);
        }else{ 
			Mage::getSingleton('core/session')->addError('Please enter Blog name!');	
			Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
        }
    }
}