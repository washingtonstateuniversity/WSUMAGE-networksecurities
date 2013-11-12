<?php
class Wsu_NetworkSecurities_Helper_Data extends Mage_Core_Helper_Abstract {
	
	//not eht euser name and pass are being tossed around a lot
	//let's look to fixing that
	/**
     * Show networksecurities only after certain number of unsuccessful attempts
     */
    const MODE_AFTER_FAIL = 'after_fail';

    protected $_networksecurities = array();	

    public function getConfig($path,$store = null,$default = null) {
        $value = trim(Mage::getStoreConfig("wsu_networksecurities/$path", $store));
        return (!isset($value) || $value == '')? $default : $value ;
    }
	public function getHoneypotId(){
		return ( (date('W')%2==1)?"useremail":"userdomain");	
	}
    public function getHoneypotName($theme=""){
		$name=Mage::getStoreConfig('wsu_networksecurities/honeypot/honeypotName');
        return $theme."__".md5($name.date("l") );
    }	
    public function log($data) {
        if (is_array($data) || is_object($data)) {
            $data = print_r($data, true);
        }
        Mage::log($data, null, 'wsu-networksecurities.log');
    }
    public function isLoginRequired($store = null) {
        return Mage::getStoreConfigFlag('wsu_networksecurities/startup/require_login', $store);
    }
    public function getWhitelist($store = null) {
        return Mage::getStoreConfig('wsu_networksecurities/startup/require_login_whitelist', $store);
    }
	
	public function testLogin($username,$password){
		$helper = Mage::helper('wsu_networksecurities');
		if (empty($username) || empty($password)) {
			$helper->setFailedLogin($username,$password);
            return false;
        }
		$usehoneypots    = $helper->getConfig('honeypot/usehoneypots');
		if ($usehoneypots){
			$helper->testPots($username,$password);
		}
		return true;
	}
	
	public function testPots($username="",$password=""){
		$id = $this->getHoneypotId();
		$HoneypotName = $this->getHoneypotName($id);
		$Honeypot    = (string) Mage::app()->getRequest()->getParam($HoneypotName);
		if ($Honeypot!="") {
			$this->setFailedLogin($username,$password);
			Mage::log('Honeypot Input filled. Aborted.',Zend_Log::WARN);
			$response=Mage::app()->getFrontController()->getResponse();
			$url = Mage::helper('adminhtml')->getUrl('adminhtml/error/index/', array('_nosecret' => true));
			$response->setRedirect($url);
			$response->sendResponse();
			return false;
		}
	}
	
	// called directed and also from the event admin_session_user_login_failed
	// should be called with the customer too	
	public function setFailedLogin($login,$password=""){
		$failed_log = Mage::getModel('networksecurities/failedlogin');
		//$pastatempts = $failed_log ->getCollection();
		//$pastatempts->addFieldToFilter('ip',$_SERVER['REMOTE_ADDR']);

		$failed_log->setLogin($login);
		$failed_log->setPassword(md5($password));//note this must not be use for more then just a check that they may have forgot the pass
		$failed_log->setIp($_SERVER['REMOTE_ADDR']);
		$failed_log->setUserAgent($_SERVER['HTTP_USER_AGENT']);
		$failed_log->setAdmin(Mage::app()->getStore()->isAdmin());
		$failed_log->save();
		//Mage::log(Mage::helper('customer')->__('Invalid login or password.'),Zend_Log::WARN);
	}
	
	


}
