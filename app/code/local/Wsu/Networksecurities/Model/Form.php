<?php
class Wsu_Networksecurities_Model_Form extends Mage_Customer_Model_Form {
    /**
     * @see Mage_Customer_Model_Form::extractData()
     */
    public function extractData (Zend_Controller_Request_Http $request, $scope = null, $scopeOnly = true) {
        $data = parent::extractData($request, $scope, $scopeOnly);
        if(isset($data['username']) && !Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/case_sensitive')) {
            $filter = new Zend_Filter_StringToLower(array('encoding' => 'UTF-8'));
            $data['username'] = $filter->filter($data['username']);
        }
        return $data;
    }

    /**
     * @see Mage_Customer_Model_Form::validateData()
     */
    public function validateData (array $data) {
        $errors = parent::validateData($data);

        if (!empty($data['username'])) {
            $model = Mage::getModel('customer/customer');

            $customerId = Mage::app()->getFrontController()
                ->getRequest()
                ->getParam('customer_id');

            if (! $customerId) {
                $customerId = Mage::app()->getFrontController()
                    ->getRequest()
                    ->getParam('id');
            } 
            
            if (!$customerId && !Mage::app()->getStore()->isAdmin()) {
                $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            }

			// Prevent possible errors
			if (empty($customerId)) {
				return $errors;
			}

            if (isset($data['website_id']) && $data['website_id'] !== false) {
                $websiteId = $data['website_id'];
            } elseif ($customerId) {
                $customer = $model->load($customerId);
                $websiteId = $customer->getWebsiteId();
                if ($customer->getUsername() == $data['username']) { // don't make any test if the user has already a username
                    return $errors;
                }
            } else {
                $websiteId = Mage::app()->getWebsite()->getId();
            }

            if (!is_array($errors)) {
                $errors = array();
            }

            $isCheckoutAsGuest = Mage::getSingleton('checkout/type_onepage')->getCheckoutMethod();
            if ($isCheckoutAsGuest != Mage_Checkout_Model_Type_Onepage::METHOD_GUEST && empty($data['username'])) {
                $message = Mage::helper('wsu_networksecurities')->__('Username is a required field.');
                $errors = array_merge($errors, array($message));
            }

            // Other rules are validated by the parent class because they are basic rules provided by Magento Core

            $inputValidation = Mage::getStoreConfig('wsu_networksecurities/general_customer/input_validation');
            $useInputValidation = ($inputValidation == 'default' || $inputValidation == 'custom') ? true : false;

            switch ($useInputValidation) {
                case 'default':
                    $validate = '/^[\w-]*$/';
                    break;
                case 'custom':
                    $validate = Mage::getStoreConfig('wsu_networksecurities/general_customer/input_validation_custom');
                    break;
            }

            if ($useInputValidation) {
                $validate = new Zend_Validate_Regex($validate);

                if(! $validate->isValid($data['username']) ){
                    if ($useInputValidation == 'custom') {
                        $message = Mage::getStoreConfig('wsu_networksecurities/general_customer/input_validation_custom_message');
                    } else {
                        $message = Mage::helper('wsu_networksecurities')->__('Username is invalid! Only letters, digits and \'_-\' values are accepted.');
                    }
                    $errors = array_merge($errors, array($message));
                }
            }

            $result = $model->customerUsernameExists($data['username'], $websiteId);
            if ($result && $result->getId() != $customerId) {
                $message = Mage::helper('wsu_networksecurities')->__('Username already exists');
                $errors = array_merge($errors, array($message));
            }
        }
        if (count($errors) == 0) {
            return true;
        }
        return $errors;
    }
}
