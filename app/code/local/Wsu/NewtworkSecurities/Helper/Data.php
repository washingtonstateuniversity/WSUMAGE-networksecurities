<?php
class Wsu_NewtworkSecurities_Helper_Data extends Mage_Core_Helper_Abstract {
	
    public function getConfig($path,$store = null,$default = null) {
        $value = trim(Mage::getStoreConfig("wsu_newtworksecurities/$path", $store));
        return (!isset($value) || $value == '')? $default : $value ;
    }
	public function getHoneypotId(){
		return ( (date('W')%2==1)?"useremail":"userdomain");	
	}
    public function getHoneypotName($theme=""){
		$name=Mage::getStoreConfig('wsu_newtworksecurities/honeypot/honeypotName');
        return $theme."__".md5($name.date("l") );
    }	
    public function log($data) {
        if (is_array($data) || is_object($data)) {
            $data = print_r($data, true);
        }
        Mage::log($data, null, 'wsu-newtworksecurities.log');
    }
    public function isLoginRequired($store = null) {
        return Mage::getStoreConfigFlag('wsu_newtworksecurities/startup/require_login', $store);
    }
    public function getWhitelist($store = null) {
        return Mage::getStoreConfig('wsu_newtworksecurities/startup/require_login_whitelist', $store);
    }
	/**
     * Show newtworksecurities only after certain number of unsuccessful attempts
     */
    const MODE_AFTER_FAIL = 'after_fail';

    /**
     * List uses Models of NewtworkSecurities
     * @var array
     */
    protected $_newtworksecurities = array();

}
