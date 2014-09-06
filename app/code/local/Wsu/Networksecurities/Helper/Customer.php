<?php
class Wsu_Networksecurities_Helper_Customer extends Mage_Core_Helper_Abstract {
	
	public function getCustomerByEmail($email, $website_id=null) { //edit
		$collection = Mage::getModel('customer/customer')->getCollection()
					->addFieldToFilter('email', $email);
		if (Mage::getStoreConfig('customer/account_share/scope') && $website_id!=null) {
			$collection->addFieldToFilter('website_id',$website_id);
		}
		return $collection->getFirstItem();
	}



	public function getCustomerByAltSSo($data) {
		$provider=isset($data['provider'])?$data['provider']:null;
		if(isset($provider)){
			$collection = Mage::getModel('customer/customer')->getCollection();
			$collection->addAttributeToSelect('sso_map'); 
			if (Mage::getStoreConfig('customer/account_share/scope') && $website_id!=null) {
				$collection->addFieldToFilter('website_id',$website_id);
			}
			
			$sso_map = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'sso_map');
			
			$collection->getSelect()
				->join(array('cus' => 'customer_entity_varchar'), 'cus.entity_id=e.entity_id')
    			->Where('cus.attribute_id='.$sso_map->getAttributeId());
				
			$username=isset($data['username'])?$data['username']:null;
			$email=isset($data['email'])?$data['email']:null;

			$loopUpMap = "";
			if(isset($username)){
				$loopUpMap .=sprintf("cus.value LIKE %s","CONCAT('%','".$provider.'":"'.$username."','%')");
			}
			if(isset($email)){
				$loopUpMap .= ($loopUpMap==""?"":" OR ").sprintf("cus.value LIKE %s","CONCAT('%','".$provider.'":"'.$email."','%')");
			}
			
			$collection->getSelect()->Where('( '.$loopUpMap.' )');
			return $collection->getFirstItem();
		}
		return null;
	}



	public function createCustomer($data) {
		$customer = Mage::getModel('customer/customer');
		$customer->setFirstname( $data['firstname'] );
		$customer->setLastname( $data['lastname'] );
		$customer->setEmail( $data['email'] );
		
		if(Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled') && isset($data['username'])){
			$customer->setUsername($data['username']);
		}
		if(isset($data['gender'])){
			$customer->setGender( $data['gender'] );
		}
		if(isset($data['dbo'])){
			$customer->setDbo( date($data['dbo']) );
		}
		
		$map=array();
		$provider=$data['provider'];
		if(isset($provider)){
			$map[$provider]=$data['email'];
			if(Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled') && isset($data['username'])){
				$map[$provider]=$data['username'];
			}
		}
		$customer->setSsoMap( json_encode($map) );		

		$newPassword = $customer->generatePassword();
		$customer->setPassword($newPassword);
		try{
			$customer->save();
		}catch(Exception $e) {}

		return $customer;
	}	
	//create customer login multisite
	public function createCustomerMultiWebsite($data, $website_id, $store_id) {
		$customer = Mage::getModel('customer/customer')->setId(null);
		
		$customer->setFirstname( $data['firstname'] );
		$customer->setLastname( $data['lastname'] );
		$customer->setEmail( $data['email'] );
		if(Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled') && isset($data['username'])){
			$customer->setUsername( $data['username'] );
		}
		if(isset($data['gender'])){
			$customer->setGender( $data['gender'] );
		}
		if(isset($data['dbo'])){
			$customer->setDbo( date($data['dbo']) );
		}
		
		$map=array();
		$provider=$data['provider'];
		if(isset($provider)){
			$map[$provider]=$data['email'];
			if(Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled') && isset($data['username'])){
				$map[$provider]=$data['username'];
			}
		}
		$customer->setSsoMap( json_encode($map) );
				
		$customer->setWebsiteId($website_id);
		$customer->setStoreId($store_id);
		
		$newPassword = $customer->generatePassword();
		$customer->setPassword($newPassword);
		try{
			$customer->save();
		}catch(Exception $e) {}
		
		if (Mage::getStoreConfig("wsu_networksecurities/${provider}_login/is_send_password_to_customer")) {
			$customer->sendPasswordReminderEmail();
		}
		if ($customer->getConfirmation()) {
			try {
				$customer->setConfirmation(null);
				$customer->save();
			}catch (Exception $e) {
				Mage::getSingleton('core/session')->addError(Mage::helper('wsu_networksecurities')->__('Error').$e->getMessage());
			}
		}
		return $customer;
	}	
    public function getResponseBody($url) {
		if(ini_get('allow_url_fopen') != 1) {
			@ini_set('allow_url_fopen', '1');
		}
		if(ini_get('allow_url_fopen') == 1) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3);           
			$contents = curl_exec($ch);            
			curl_close($ch);
		}else{
		   	$contents=file_get_contents($url);
		}
		return $contents;
	}
	public function getShownPositions() {
			$shownpositions = Mage::getStoreConfig('wsu_networksecurities/general_sso/position',Mage::app()->getStore()->getId());
			$shownpositions = explode(',',$shownpositions);
			return $shownpositions;
	}
	public function getPerResultStatus($result) {
		$result = str_replace( array('{','}','"',':'),array( '','','',','), $result );
		$rs = explode(",", $result);
		if($rs[10]) {
			return $rs[10];
		}else{
			return "";
		}
	}
	public function getPerEmail($result) {
		$result = str_replace( array('"',':'),array( '',','), $result );
		$rs = explode(",", $result);
		if($rs[8]) {
			return $rs[8];
		}else{
			return "";
		}
	}	
	


	public function getDirectLoginUrl() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('sociallogin/facebooklogin/login', array('_secure'=>$isSecure));
	}
	public function getLoginUrl() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('customer/account/login', array('_secure'=>$isSecure));
	}
	public function getEditUrl() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('customer/account/edit', array('_secure'=>$isSecure));
	}




	
	
	public function setJsRedirect($url=null){
		$html="Failed to get a proper url redirect";
		if(!is_null($url)){
			$html="<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e) {window.opener.location.href=\"".$url."\"} window.close();</script>";
		}
		Mage::app()->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')
			->setBody($html);	
	}
	public function _loginPostRedirect() {
        $session = Mage::getSingleton('customer/session');
        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }else if ( $session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl() ) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }else{ 
			if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }
        return $session->getBeforeAuthUrl(true);
    }
	
}
