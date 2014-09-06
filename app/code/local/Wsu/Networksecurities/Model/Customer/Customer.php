<?php
class Wsu_Networksecurities_Model_Customer_Customer extends Mage_Customer_Model_Customer {
    /**
     * Authenticate customer
     *
     * @param  string $login email or username
     * @param  string $password
     * @throws Mage_Core_Exception
     * @return true
     *
     */
    public function authenticate($username, $password) {
		//$login = trim(mb_convert_kana($login, 'as'));
		$actived = trim(Mage::getStoreConfig('wsu_networksecurities/ldap/customerlogin/activeldap'));
		/*if (!$actived) { //CHECK MAGENTO CONNECT
		        if (!$this->validatePassword($password)) {
					Mage::helper('wsu_networksecurities')->setFailedLogin($username,$password);
				}
				return parent::authenticate($username, $password);
		}*/
		if(Zend_Validate::is($username, 'EmailAddress')) { 
			$this->loadByEmail($username); 
		}else if (Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled')) { 
			$this->loadByUsername($username);    
		} 

        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            Mage::helper('wsu_networksecurities')->setFailedLogin($username,$password);
        }
        Mage::dispatchEvent('customer_customer_authenticated', array(
           'model'    => $this,
           'password' => $password,
        ));

        return true;
    }

	/**
	 * Load customer by username
	 *
	 * @param   string $customerUsername
	 * @return  Mage_Customer_Model_Customer
	 */
	public function loadByUsername($customerUsername) {
		$this->_getResource()->loadByUsername($this, $customerUsername);
		return $this;
	}

    /**
     * Test if username already exists
     * 
     * @param string $username
     * @param int $websiteId
     * @return boolean
     */
    public function customerUsernameExists($username, $websiteId = null) {
        if(!is_null($websiteId)) {
            $this->setWebsiteId($websiteId);
        }
        
        $this->loadByUsername($username);
        if ($this->getId()) {
            return $this;
        }
        return false;
    }

	public function hasSsoMap($customer,$data) {
		if(!$customer->getId()){
			return false;	
		}
		$_customer=Mage::getModel('customer/customer')->load($customer->getId());
		$ssomap=$_customer->getSsoMap();
        $map = isset($ssomap) ? $ssomap : array();
		if(is_string($map)){
			$map = (array)json_decode($map);
		}
		$provider=$data['provider'];
		return isset($provider) && isset($map[$provider]);
	}

	
	public function addSsoMap($customer,$data) {
		$map = $customer->getSsoMap();
		if(isset($map)){
			$map = (array)json_decode($map);
		}
		$provider=$data['provider'];
		if(isset($provider)){
			if(!isset($map[$provider])){
				$map[$provider]=$data['email'];
				if(Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled') && isset($data['username'])){
					$map[$provider]=$data['username'];
				}
			}
		}
		$customer->setSsoMap( json_encode($map) );
		$customer->save();
		return $customer;
	}
	
}
