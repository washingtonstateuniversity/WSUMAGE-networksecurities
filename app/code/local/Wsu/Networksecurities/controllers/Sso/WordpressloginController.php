<?php
class Wsu_Networksecurities_Sso_WordpressloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {

	/**
	* getToken and call profile user WordPress
	**/
    public function loginAction($name_blog) {
		$wp = Mage::getModel('wsu_networksecurities/sso_wordpresslogin')->getProvider();       
		$userId = $wp->mode;
		if(!$userId) {
            $wp_session = Mage::getModel('wsu_networksecurities/sso_wordpresslogin')->setIdlogin($wp, $name_blog);
            $url = $wp_session->authUrl();
			$this->_redirectUrl($url);
		}else{ 
			if (!$wp->validate()) {                
				$wp_session = Mage::getModel('wsu_networksecurities/sso_wordpresslogin')->setIdlogin($wp, $name_blog);
				$url = $wp_session->authUrl();
				$this->_redirectUrl($url);
            }else{ $user_info = $wp->getAttributes();                 
                if(count($user_info)) {
					$user_info['provider']="wordpress";
					$this->handleCustomer($user_info);
                }else{ 
					Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');			
					Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
                }
            }           
        }
    }
    
	public function makeCustomerData($user_info) {
		$data = array();

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
			$last_name = '_wp';
		}
		
		$data['provider']=$user_info['provider'];
		$data['email']=$email;
		$data['firstname']=$frist_name;
		$data['lastname']=$last_name;

		return $data;
	}	
	
	
	
	
    public function setBlockAction() {             
        /*$template =  $this->getLayout()->createBlock('sociallogin/wordpresslogin')
                ->setTemplate('sociallogin/au_wp.phtml')->toHtml();
        echo $template;*/
		$this->loadLayout();
		$this->renderLayout();
    }
    
    public function setBlogNameAction() {
        $data = $this->getRequest()->getPost();		
		$name = $data['name'];
        if($name) {            
            $url = Mage::getModel('wsu_networksecurities/sso_wordpresslogin')->getLoginUrl($name);			
            $this->_redirectUrl($url);
        }else{ 
			Mage::getSingleton('core/session')->addError('Please enter Blog name!');	
            Mage::helper('wsu_networksecurities/customer')->setJsRedirect(Mage::getBaseUrl());
        }
    }
}