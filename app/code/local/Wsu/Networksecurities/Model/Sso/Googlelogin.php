<?php
class Wsu_Networksecurities_Model_Sso_Googlelogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	var $_providerName = 'google';	
	
	public function getConsumerKey() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/google_login/consumer_key'));
	}
	public function getConsumerSecret() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/google_login/consumer_secret'));
	}
	public function getRedirectUri() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return Mage::getUrl('sociallogin/googlelogin/user',array('_secure'=>$isSecure));
	}

	public function createProvider() {

		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'service'.DS.'Google_ServiceResource.php');
		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'service'.DS.'Google_Service.php');
		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'service'.DS.'Google_Model.php');
		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'contrib'.DS.'Google_Oauth2Service.php');
		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'Google_Client.php');

		$google = new Google_Client(array(
				// True if objects should be returned by the service classes.
				// False if associative arrays should be returned (default behavior).
				'use_objects' => false,
			  
				// The application_name is included in the User-Agent HTTP header.
				'application_name' => Mage::app()->getStore()->getName()." sign in",
			
				// OAuth2 Settings, you can get these keys at https://code.google.com/apis/console
				'oauth2_client_id' => '',
				'oauth2_client_secret' => '',
				'oauth2_redirect_uri' => '',
			
				// The developer key, you get this at https://code.google.com/apis/console
				'developer_key' => '',
			  
				// Site name to show in the Google's OAuth 1 authentication screen.
				'site_name' => Mage::app()->getStore()->getHomeUrl(),
			
				// Which Authentication, Storage and HTTP IO classes to use.
				'authClass'    => 'Google_OAuth2',
				'ioClass'      => 'Google_CurlIO',
				'cacheClass'   => 'Google_FileCache',
			
				// Don't change these unless you're working against a special development or testing environment.
				'basePath' => 'https://www.googleapis.com',
			
				// IO Class dependent configuration, you only have to configure the values
				// for the class that was configured as the ioClass above
				'ioFileCache_directory'  =>
					(function_exists('sys_get_temp_dir') ?
						sys_get_temp_dir() . '/Google_Client' :
					'/tmp/Google_Client'),
			
				// Definition of service specific values like scopes, oauth token URLs, etc
				'services' => array(
				  'analytics' => array('scope' => 'https://www.googleapis.com/auth/analytics.readonly'),
				  'calendar' => array(
					  'scope' => array(
						  "https://www.googleapis.com/auth/calendar",
						  "https://www.googleapis.com/auth/calendar.readonly",
					  )
				  ),
				  'books' => array('scope' => 'https://www.googleapis.com/auth/books'),
				  'latitude' => array(
					  'scope' => array(
						  'https://www.googleapis.com/auth/latitude.all.best',
						  'https://www.googleapis.com/auth/latitude.all.city',
					  )
				  ),
				  'moderator' => array('scope' => 'https://www.googleapis.com/auth/moderator'),
				  'oauth2' => array(
					  'scope' => array(
						  'https://www.googleapis.com/auth/userinfo.profile',
						  'https://www.googleapis.com/auth/userinfo.email',
					  )
				  ),
				  'plus' => array('scope' => 'https://www.googleapis.com/auth/plus.login'),
				  'siteVerification' => array('scope' => 'https://www.googleapis.com/auth/siteverification'),
				  'tasks' => array('scope' => 'https://www.googleapis.com/auth/tasks'),
				  'urlshortener' => array('scope' => 'https://www.googleapis.com/auth/urlshortener')
				)
			));
		//var_dump(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'service'.DS.'Google_ServiceResource.php');
		$google->setClientId($this->getConsumerKey());
		$google->setClientSecret($this->getConsumerSecret());
		$google->setRedirectUri($this->getRedirectUri());
		$google->setApplicationName(Mage::app()->getStore()->getName()." sign in");
		//var_dump($google);die();
		return $google;
	}
}
  
