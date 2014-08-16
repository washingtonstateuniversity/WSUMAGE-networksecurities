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
    public function authenticate($username, $password){
		//$login = trim(mb_convert_kana($login, 'as'));
		$actived = trim(Mage::getStoreConfig('wsu_networksecurities/ldap/customerlogin/activeldap'));
		if (!$actived){ //CHECK MAGENTO CONNECT
		        if (!$this->validatePassword($password)) {
					Mage::helper('wsu_networksecurities')->setFailedLogin($username,$password);
				}
				return parent::authenticate($username, $password);
		}
		
		if(Zend_Validate::is($username, 'EmailAddress')){ 
			$this->loadByEmail($username); 
		}else if (Mage::getStoreConfigFlag('username/general/enabled')) { 
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



}
