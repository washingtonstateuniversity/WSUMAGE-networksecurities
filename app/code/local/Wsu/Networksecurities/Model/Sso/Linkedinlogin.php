<?php
class Wsu_Networksecurities_Model_Sso_Linkedinlogin extends Zend_Oauth_Consumer {
	protected $_options = null;
	public function __construct() {
		$this->_config = new Zend_Oauth_Config;		
		$this->_options = array(
			'consumerKey'       => Mage::helper('wsu_networksecurities/customer')->getLinkedConsumerKey(),
			'consumerSecret'    => Mage::helper('wsu_networksecurities/customer')->getLinkedConsumerSecret(),
			'version'           => '1.0',
			'requestTokenUrl'   => 'https://api.linkedin.com/uas/oauth/requestToken?scope=r_emailaddress',
			'accessTokenUrl'    => 'https://api.linkedin.com/uas/oauth/accessToken',
			'authorizeUrl'      => 'https://www.linkedin.com/uas/oauth/authenticate'
		);
		$this->_config->setOptions($this->_options);
	}
	public function setCallbackUrl($url) {
		$this->_config->setCallbackUrl($url);
	}
	public function getOptions() {
		return $this->_options ;
	}
}