<?php
class Wsu_Networksecurities_Model_Customer extends Mage_Customer_Model_Customer{

    /**
     * Authenticate customer
     *
     * @param  string $login
     * @param  string $password
     * @return true
     * @throws Exception
     */
	 
	 //this may need to move to the wsu_networksecurities/customer/customer model
	 
	 
    public function authenticate($login, $password) {   
        if(Zend_Validate::is($login, 'EmailAddress')){
            $this->loadByEmail($login);
        }else if (Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled')) {
            $this->loadByUsername($login);   
        }

        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
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
     * @return Diglin_Username_Model_Customer|boolean
     */
    public function customerUsernameExists($username, $websiteId = null) {
        if(!is_null($websiteId)){
            $this->setWebsiteId($websiteId);
        }
        
        $this->loadByUsername($username);
        if ($this->getId()) {
            return $this;
        }
        return false;
    }
}