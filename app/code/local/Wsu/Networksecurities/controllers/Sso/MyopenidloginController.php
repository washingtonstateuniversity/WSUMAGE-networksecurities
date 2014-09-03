<?php
class Wsu_Networksecurities_Sso_MyopenidloginController extends Mage_Core_Controller_Front_Action{
   
	public function loginAction() {    
		$customerHelper = Mage::helper('wsu_networksecurities/customer'); 
		$identity = $this->getRequest()->getPost('identity');
		$my = Mage::getModel('wsu_networksecurities/sso_myopenidlogin')->newProvider();  
		Mage::getSingleton('core/session')->setData('identity',$identity);		
		$userId = $my->mode;
		if(!$userId) {
			$my = Mage::getModel('wsu_networksecurities/sso_myopenidlogin')->setOpenIdlogin($my,$identity);
			try{
				$url = $my->authUrl();
			}catch(Exception $e) {
				Mage::getSingleton('core/session')->addError('Username not exacted');
				$customerHelper->setJsRedirect(Mage::getBaseUrl());
			}
			$this->_redirectUrl($url);
		}else{ if (!$my->validate()) {                
                $my = Mage::getModel('wsu_networksecurities/sso_myopenidlogin')->setOpenIdlogin($my,$identity);
                try{
					$url = $my->authUrl();
				}catch(Exception $e) {
					Mage::getSingleton('core/session')->addError('Username not exacted');
					$customerHelper->setJsRedirect(Mage::getBaseUrl());
				}
                $customerHelper->setJsRedirect(Mage::getBaseUrl());
            }else{ //$user_info = $my->getAttributes(); 
				$user_info = $my->data;
				if(count($user_info)) {
					$user_info['provider']="myopenid";
					$this->handleCustomer($user_info);
                }else{
                   Mage::getSingleton('core/session')->addError('User has not shared information so login fail!');			
                   $customerHelper->setJsRedirect(Mage::getBaseUrl());
                }
            }           
        }
    }
	
	public function makeCustomerData($user_info) {
		$data = array();
		$identity = $user_info['openid_identity'];
		$length = strlen($identity);
		$httpLen = strlen("http://");
		$userAccount = substr($identity,$httpLen,$length-1-$httpLen);
		$userArray = explode( '.', $userAccount,2);
		$firstname = $userArray[0];
		$lastname ="";
		$email = $firstname."@".$userArray[1];
		$authorId = $email;
		
		$data['provider']=$user_info['provider'];
		$data['email']=$email;
		$data['firstname']=$frist_name;
		$data['lastname']=$last_name;
		$data['authorId']=$authorId;

		return $data;
	}	
	/**
	* return template au_wp.phtml
	**/
    public function setBlockAction() {             
		$this->loadLayout();
		$this->renderLayout();
        /*$template =  $this->getLayout()->createBlock('sociallogin/myopenidlogin')
                ->setTemplate('sociallogin/au_op.phtml')->toHtml();
        echo $template;*/
    }
}