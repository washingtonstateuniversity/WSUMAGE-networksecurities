<?php

require_once 'Mage/Customer/controllers/AccountController.php';
class Wsu_Networksecurities_AccountController extends Mage_Customer_AccountController {
    /**
     * Login post action
     */
    public function loginPostAction() {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                }
                catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value   = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage()." The email or password is invaild.";
                            break;
                        default:
                            $message = $e->getMessage()." - please contact support.";
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                }
                catch (Exception $e) {
                // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            }else{ $session->addError($this->__('Login and password are required.'));
            }
        }
        $this->_loginPostRedirect();
    }
    /**
     * Create customer account action
     */
    public function createPostAction() {
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
		$request = $this->getRequest();
        if ($request->isPost()) {
            $errors = array();
            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }
            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')->setEntity($customer);
            $customerData = $customerForm->extractData($request);
            if ($request->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }
            /**
             * Initialize customer group id
             */
            $customer->getGroupId();
            if ($request->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address     = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')->setEntity($address);
                $addressData   = $addressForm->extractData($request, 'address', false);
                $addressErrors = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)->setIsDefaultBilling($request->getParam('default_billing', false))->setIsDefaultShipping($request->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);
                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                }else{ $errors = array_merge($errors, $addressErrors);
                }
            }
            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                }else{ 

					$customerForm->compactData($customerData);
                    $customer->setPassword($request->getPost('password'));
                    $customer->setConfirmation($request->getPost('confirmation'));
					$customer->setPasswordConfirmation($request->getPost('confirmation'));					
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }
									
                $validationResult = count($errors) == 0;
                if (true === $validationResult) {
                    $customer->save();
                    Mage::dispatchEvent('customer_register_success', array(
                        'account_controller' => $this,
                        'customer' => $customer
                    ));
                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl(), Mage::app()->getStore()->getId());
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->_redirectSuccess(Mage::getUrl('*/*/index', array(
                            '_secure' => true
                        )));
                        return;
                    }else{ $session->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        $this->_redirectSuccess($url);
                        return;
                    }
                }else{ $session->setCustomerFormData($request->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    }else{ $session->addError($this->__('Invalid customer data'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($request->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url     = Mage::getUrl('customer/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                }else{ $message = $e->getMessage();
                }
                $session->addError($message);
            }
            catch (Exception $e) {
                $session->setCustomerFormData($request->getPost())->addException($e, $this->__('Cannot save the customer.'));
            }
        }
        $this->_redirectError(Mage::getUrl('*/*/create', array(
            '_secure' => true
        )));
    }
    /**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction() {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
			/** @var $customer Wsu_Networksecurties_Model_Customer */
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByUsername($email);
            /** @var $customer Mage_Customer_Model_Customer */
			if (!$customer->getId() && !Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
                $this->_redirect('*/*/forgotpassword');
                return;
            } else if (!$customer->getId()) {
                $customer = Mage::getModel('customer/customer')
					->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
					->loadByEmail($email);
            }
            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                }
                catch (Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    $this->_redirect('*/*/forgotpassword');
                    return;
                }
            }
            $this->_getSession()
				->addSuccess(Mage::helper('customer')->__('If there is an account associated with %s you will receive an email with a link to reset your password.', Mage::helper('customer')->htmlEscape($email)));
            $this->_redirect('*/*/');
            return;
        }else{ $this->_getSession()->addError($this->__('Please enter your email.'));
            $this->_redirect('*/*/forgotpassword');
            return;
        }
    }
    /**
     * Forgot customer account information page
     */
    public function editAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $block = $this->getLayout()->getBlock('customer_edit');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $data     = $this->_getSession()->getCustomerFormData(true);
        $customer = $this->_getSession()->getCustomer();
        if (!empty($data)) {
            $customer->addData($data);
        }
        if ($this->getRequest()->getParam('changepass') == 1) {
            $customer->setChangePassword(1);
        }
        $this->getLayout()->getBlock('head')->setTitle($this->__('Account Information'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }
	
	
    /**
     * Display the access request form
     */
    public function make_requestAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $block = $this->getLayout()->getBlock('customer_edit');
		//display the form yo
        $this->getLayout()->getBlock('head')->setTitle($this->__('Request Form'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }
    /**
     * Take the access request
     */
    public function take_requestAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $block = $this->getLayout()->getBlock('customer_edit');
		//if pass ok
		//then email from system/config
		//else display error
        $this->getLayout()->getBlock('head')->setTitle($this->__('Request Form'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }
	
	
	
}
