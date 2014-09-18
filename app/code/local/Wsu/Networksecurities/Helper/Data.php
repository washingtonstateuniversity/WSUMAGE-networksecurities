<?php
class Wsu_Networksecurities_Helper_Data extends Mage_Core_Helper_Abstract {
    const MODE_AFTER_FAIL = 'after_fail';
    const MODE_ALWAYSL = 'always';
	const IPMODE_EXCLUDE = 'exclude';
	const IPMODE_INCLUDE = 'include';
	
    protected $_networksecurities = array();	


	public function adminInFrontend(){
		//check if adminhtml cookie is set
		if(array_key_exists('adminhtml', $_COOKIE)){
		   //get session path and add dir seperator and content field of cookie as data name with magento "sess_" prefix
		   $sessionFilePath = Mage::getBaseDir('session').DS.'sess_'.$_COOKIE['adminhtml'];
		   //write content of file in var
		   $sessionFile = file_get_contents($sessionFilePath);
		
		   //save old session
		   $oldSession = $_SESSION;
		   //decode adminhtml session
		   session_decode($sessionFile);
		   //save session data from $_SESSION
		   $adminSessionData = $_SESSION;
		   //set old session back to current session
		   $_SESSION = $oldSession;
		   if(array_key_exists('user', $adminSessionData['admin'])){
			  //save Mage_Admin_Model_User object in var
			  $adminUserObj = $adminSessionData['admin']['user'];
			  return true;
		   }
		}	
		return false;
	}



    public function isPersistentMustBeEnabled () {
        return Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled')
            && Mage::helper('core')->isModuleEnabled('persistent')
            && Mage::helper('core')->isModuleOutputEnabled('persistent')
            && Mage::helper('persistent')->isEnabled();
    }
    public function getConfig($path,$store = null,$default = null) {
        $value = trim(Mage::getStoreConfig("wsu_networksecurities/$path", $store));
        return (!isset($value) || $value == '')? $default : $value ;
    }
	public function getHoneypotId() {
		return ( (date('W')%2==1)?"useremail":"userdomain");	
	}
    public function getHoneypotName($theme="") {
		$name=Mage::getStoreConfig('wsu_networksecurities/honeypot/honeypotName');
        return $theme."__".md5($name.date("l") );
    }	
    public function log($data,$level=Zend_Log::NOTICE,$display=true) {
		$logging=$this->getConfig("general/logging",null,"full");
        if (is_array($data) || is_object($data)) {
            $data = print_r($data, true);
        }
		$logFile="adminlog.txt";
		
		if( $level == Zend_Log::ERR ){//&& in_array( $logging, array('light','full') ) ) {
			Mage::log($data,$level,$logFile);
			if($display){
				Mage::getSingleton('adminhtml/session')->addError($data);
			}
		}
		if($level == Zend_Log::NOTICE && in_array( $logging, array('full') ) ) {
			Mage::log($data,$level,$logFile);
			if($display){
				Mage::getSingleton('adminhtml/session')->addNotice($data);
			}
		}
		if($level == Zend_Log::WARN && in_array( $logging, array('full') ) ) {
			Mage::log($data,$level,$logFile);
			if($display){
				Mage::getSingleton('adminhtml/session')->addWarning($data);
			}
		}

    }
    public function isLoginRequired($store = null) {
        return Mage::getStoreConfigFlag('wsu_networksecurities/startup/require_login', $store);
    }
    public function getWhitelist($store = null) {
        return Mage::getStoreConfig('wsu_networksecurities/startup/require_login_whitelist', $store);
    }

    public function filterFrontIp($controllerAction) {
		$use_ipfilter=Mage::getStoreConfig('wsu_networksecurities/startup/use_ipfilter_frontend');
		
		if($use_ipfilter==1) {
			$HELPER = Mage::helper('wsu_networksecurities');
			$ip = $HELPER->get_ip_address();
			$ipfilter=Mage::getStoreConfig('wsu_networksecurities/startup/ipfilter_frontend');
			$mode=Mage::getStoreConfig('wsu_networksecurities/startup/ipfiltermode_frontend');
			$redirection=Mage::getStoreConfig('wsu_networksecurities/startup/ipfilter_redirection_frontend');
			
			$match=preg_match('/'.$ipfilter.'/',$ip);
			if($match>0 && $mode==Wsu_Networksecurities_Helper_Data::IPMODE_EXCLUDE || $match==0 && $mode==Wsu_Networksecurities_Helper_Data::IPMODE_INCLUDE) {
				if(strpos(Mage::helper('core/url')->getCurrentUrl(),$redirection)===false) {
					$controllerAction->getResponse()->setRedirect(Mage::getUrl($redirection));
					$controllerAction->getResponse()->sendResponse();
					exit;
				}
			}
		}
    }




	public function testpot() {
		$id = $this->getHoneypotId();
		$HoneypotName = $this->getHoneypotName($id);
		$Honeypot    = (string) Mage::app()->getRequest()->getParam($HoneypotName);
		if ($Honeypot!="") {
			/*Mage::log('Honeypot Input filled. Aborted.',Zend_Log::WARN);
			$response=Mage::app()->getFrontController()->getResponse();
			$url = Mage::helper('adminhtml')->getUrl('adminhtml/error/index/', array('_nosecret' => true));
			$response->setRedirect($url);
			$response->sendResponse();*/
			return false;
		}
		return true;
	}



	
	public function testLogin($username,$password) {
		$helper = Mage::helper('wsu_networksecurities');
		if (empty($username) || empty($password)) {
			$helper->setFailedLogin($username,$password);
            return false;
        }
		$usehoneypots    = $helper->getConfig('honeypot/usehoneypots');
		if ($usehoneypots) {
			$helper->testloginPots($username,$password);
		}
		
		$use_ipfilter=Mage::getStoreConfig('wsu_networksecurities/genadmin_settings/use_ipfilter_admin');
		
		if($use_ipfilter==1) {
			$ip = $helper->get_ip_address();
			$ipfilter=Mage::getStoreConfig('wsu_networksecurities/genadmin_settings/ipfilter_admin');
			$mode=Mage::getStoreConfig('wsu_networksecurities/genadmin_settings/ipfiltermode_admin');			
			$match=preg_match('/'.$ipfilter.'/',$ip);
			if($match>0 && $mode==Wsu_Networksecurities_Helper_Data::IPMODE_EXCLUDE || $match==0 && $mode==Wsu_Networksecurities_Helper_Data::IPMODE_INCLUDE) {
				Mage::getSingleton('core/session')->addError('You are trying to access the admin from an unsecure connection.  You may need to VPN in.  Please contact your admin.');
				return false;
			}
		}
		return true;
	}
	
	public function testloginPots($username="",$password="") {
		$id = $this->getHoneypotId();
		$HoneypotName = $this->getHoneypotName($id);
		$Honeypot    = (string) Mage::app()->getRequest()->getParam($HoneypotName);
		if ($Honeypot!="") {
			$this->setFailedLogin($username,$password);
			Mage::log('Honeypot Input filled. Aborted.',Zend_Log::WARN);
			$response=Mage::app()->getFrontController()->getResponse();
			$url = Mage::helper('adminhtml')->getUrl('adminhtml/error/index/', array('_nosecret' => true));
			$response->setRedirect($url);
			//$this->setBlacklist(Mage::helper('wsu_networksecurities')->get_ip_address());
			//note this needs to be done affter the honeypot level check.
			$response->sendResponse();
			return false;
		}
	}
	
	
	// called directed and also from the event admin_session_user_login_failed
	// should be called with the customer too	
	public function setFailedLogin($login,$password="") {
		$failed_log = Mage::getModel('wsu_networksecurities/failedlogin');
		
		$request = Mage::app()->getRequest();
		
		
		//$pastatempts->addFieldToFilter('ip',$_SERVER['REMOTE_ADDR']);
		if(is_object($login)) {
			$login=$login->getUsername();	
		}
		if(is_array($login)) {
			
			if(isset($login['password'])){
				$password=$login['password'];
			}
			$login=$login['username'];
		}
		if(is_null($login)) {
			$r_login = $request->getParam('login');
			if(isset($r_login)) {
				$login=$r_login['username'];
			}
		}
		$HELPER = Mage::helper('wsu_networksecurities');
		$ip = $HELPER->get_ip_address();
		$failed_log->setLogin($login);
		$pass=($password!="")?md5($password):"failed-to-provide";
		
		
		$failed_log->setPassword($pass);//note this must not be use for more then just a check that they may have forgot the pass
		$failed_log->setIp($ip);
		$failed_log->setUserAgent($_SERVER['HTTP_USER_AGENT']);
		$failed_log->setAdmin(Mage::app()->getStore()->isAdmin());
		$failed_log->save();
		$cookie = Mage::getSingleton('core/cookie');
		$count=1;
		$userpasshash=$cookie->get('userpasshash');
		if( isset( $userpasshash ) ) {
			$old=explode(':', $userpasshash);
			$count=(int)end($old)+1;
		}
		#this is to send wouldbe level hackers on a runaround
		$cookie->set('userpasshash', md5(time()).":".$count ,time()+86400,'/');
		$pastattempts = $failed_log ->getCollection()
			->addFieldToSelect('*')
    		->addFieldToFilter('ip', $ip)
			->getSize();
		//var_dump($pastatempts);die();
		
		
		$useblacklist = $HELPER->getConfig('blacklist/useblacklist');
		if($useblacklist) {
			$limit = $HELPER->getConfig('blacklist/limiter');
			if( $pastattempts>=$limit ) {
				$this->setBlacklist($ip);
			}
		}
		//Mage::log(Mage::helper('customer')->__('Invalid login or password.'),Zend_Log::WARN);
	}
	
	// called directed and also from the event admin_session_user_login_failed
	// should be called with the customer too	
	public function setBlacklist($ip) {
		$blacklist = Mage::getModel('wsu_networksecurities/blacklist');
		$ip = Mage::helper('wsu_networksecurities')->get_ip_address();
		$blacklist->setIp($ip);
		$blacklist->setAdmin(Mage::app()->getStore()->isAdmin());
		$blacklist->save();
		$cookie = Mage::getSingleton('core/cookie');
		$count=1;
		$userBLhash=$cookie->get('userBLhash');
		if( isset( $userBLhash ) ) {
			$old=explode(':',$userBLhash);
			$count=(int)end($old)+1;
		}
		#this is to send wouldbe level hackers on a runaround
		$cookie->set('userBLhash', md5(time()).":".$count ,time()+86400,'/');
		//Mage::log(Mage::helper('customer')->__('Invalid login or password.'),Zend_Log::WARN);
	}

	public function getFailed($ip) {
		$failed_log = Mage::getModel('wsu_networksecurities/failedlogin');
		$list = $failed_log ->getCollection()
			->addFieldToSelect('*')
    		->addFieldToFilter('ip', $ip);
			
			return $list;
	}
	public function getBlacklist($ip) {
		$blacklist = Mage::getModel('wsu_networksecurities/blacklist');
		$list = $blacklist ->getCollection()
			->addFieldToSelect('*')
    		->addFieldToFilter('ip', $ip);
			
			return $list;	
	}
	public function getBlackListMessage(){
		$html="You must contact an admin to get unblocked.  There is no time limit";
		return $html;
	}
	
	public function deleteFailed($params) {}
	public function deleteBlacklist($params) {}
	
	
	
	public function get_ip_address() {
		$ip_keys = array(
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'HTTP_CLIENT_IP',
			'REMOTE_ADDR'
		);
		$foundIP=false;
		//var_dump($_SERVER);
		foreach ($ip_keys as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				//var_dump($_SERVER[$key]);var_dump($key);
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					// trim for safety measures
					$ip = trim($ip);
					// attempt to validate IP
					if ($this->validate_ip($ip)) {
						$foundIP=$ip;
						//var_dump($ip);
						//print('--'.$key.'<br/>');
					}
				}
			}
		}
		if($foundIP) {
			return $foundIP;
		}
		
		
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
	}
	/**
	 * Ensures an ip address is both a valid IP and does not fall within
	 * a private network range.
	 */
	public function validate_ip($ip) {
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
			return false;
		}
		return true;
	}
	
	
	public function captchaAvailable() {
		if (Mage::helper('core')->isModuleEnabled('Wsu_Networksecurities')) {
			return class_exists('Zend_Service_ReCaptcha') && Mage::getStoreConfig('wsu_networksecurities/captcha/public_key') && Mage::getStoreConfig('wsu_networksecurities/captcha/private_key') && Mage::getStoreConfig('wsu_networksecurities/captcha/mode') != "off";
		}
		return false;
	}
	public function getCaptcha() {
		$pubKey  = Mage::getStoreConfig('wsu_networksecurities/captcha/public_key');
		$privKey = Mage::getStoreConfig('wsu_networksecurities/captcha/private_key');
		if ($pubKey && $privKey) {
			$recaptcha = Mage::getModel('wsu_networksecurities/captcha');
			$recaptcha->setPublicKey($pubKey);
			$recaptcha->setPrivateKey($privKey);
			$theme = Mage::getStoreConfig('wsu_networksecurities/captcha/theme');
			if ($theme) {
				$recaptcha->setOption('theme', $theme);
			}
			$language = Mage::getStoreConfig('wsu_networksecurities/captcha/language');
			if ($language) {
				$recaptcha->setOption('lang', $language);
			}
		}
		return $recaptcha;
	}

	
}
