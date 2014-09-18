<?php
class Wsu_Networksecurities_Sso_LivejournalloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {
	
	public function loginAction() {  
		$customerHelper = Mage::helper('wsu_networksecurities/customer');    
		$identity = $this->getRequest()->getPost('identity');
		Mage::getSingleton('core/session')->setData('identity',$identity);
		$provider = Mage::getModel('wsu_networksecurities/sso_livejournallogin')->getProvider();
		/*$my->required = array(
        'namePerson/first',
        'namePerson/last',
        'namePerson/friendly',
        'contact/email',
		'namePerson' 
        );*/
		Mage::getSingleton('core/session')->setData('identity',$identity);
		$userId = $provider->mode;       	
		if(!$userId) {
            $provider = Mage::getModel('wsu_networksecurities/sso_livejournallogin')->setIdlogin($provider,$identity);
			try{
				$url = $provider->authUrl();
			}catch(Exception $e) {
				Mage::getSingleton('core/session')->addError('Username not exacted');
				$customerHelper->setJsRedirect(Mage::getBaseUrl());
			}
			$this->_redirectUrl($url);
		}else{ 
			if (!$provider->validate()) { 
               $provider = Mage::getModel('wsu_networksecurities/sso_livejournallogin')->setIdlogin($provider,$identity);
                try{
					$url = $provider->authUrl();
				}catch(Exception $e) {
					Mage::getSingleton('core/session')->addError('Username not exacted');			
					$customerHelper->setJsRedirect(Mage::getBaseUrl());
				}
                $this->_redirectUrl($url);
            }else{ // $user_info = $my->getAttributes();
				$user_info = $provider->data;
                if(count($user_info)) {
					$user_info['provider']="livejournallogin";
					$this->handleCustomer($user_info);
                }else{ 
					Mage::getSingleton('core/session')->addError('User has not shared information so login fail!');			
					Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
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
		$email = $firtname."@".$userArray[1];

		$data['provider']=$user_info['provider'];
		$data['email']=$email;
		$data['firstname']=$firstname;
		$data['lastname']=$lastname;
		$data['authorId']=$email;

		return $data;
	}

    public function formAction() {
		$this->loadLayout();
		$this->renderLayout();
    }
}