<?php

class Wsu_Networksecurities_Sso_AlloginController extends Wsu_Networksecurities_Sso_Abstract {

    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$aol = Mage::getModel('wsu_networksecurities/sso_allogin')->newAol();       
		$userId = $aol->mode;        
		$coreSession = Mage::getSingleton('core/session');		
		if(!$userId) {
            $aol_session = Mage::getModel('wsu_networksecurities/sso_allogin')->setAolIdlogin($aol);
            $url = $aol_session->authUrl();
			$this->_redirectUrl($url);
		}else{
			if (!$aol->validate()) {                
				$aol_session = Mage::getModel('wsu_networksecurities/sso_allogin')->setAolIdlogin($aol);
                $url = $aol_session->authUrl();
                $this->_redirectUrl($url);
            }else{
				$user_info = $aol->getAttributes();                 
                if(count($user_info)) {
					$data = $this->makeCustomerData($user_info);
					//get website_id and sote_id of each stores
					$store_id = Mage::app()->getStore()->getStoreId();//add
					$website_id = Mage::app()->getStore()->getWebsiteId();//add

                    $customer = $customerHelper->getCustomerByEmail($data['email'], $website_id);
					
                    if(!$customer || !$customer->getId()) {
						$customer = $customer->getCustomerAltSSo($customer,$data);
						if(!$customer || !$customer->getId()) {
							$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
						}
                    }else{
						$_customer = $customer->getCustomerAltSSo($customer,$data);
						if(!$_customer || !$_customer->getId()) {
							$customer = $customer->addSsoMap($customer,$data);
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