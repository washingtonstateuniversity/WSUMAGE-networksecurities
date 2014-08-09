<?php
/**
 * Admin observer model
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Wsu_Networksecurities_Model_Observer extends Mage_Admin_Model_Observer {
    const FLAG_NO_LOGIN = 'no-login';



	
	public function appendHoneypot($observer){
		echo "TEST!!!!!!!!!!!!!";
		//$layout=Mage::getSingleton('core/layout');
		//if($layout!=null && !empty($layout)){
			$update = Mage::getSingleton('core/layout')->getUpdate();
			//$update = $observer->getEvent()->getLayout()->getUpdate();
            $update->addHandle('networksecurities.honeypot');
    }
	


    /**
     * call rules
     */
    public function controllerActionPredispatchCustomerAccountCreatepost() {
		$HELPER = Mage::helper('wsu_networksecurities');
        if ($HELPER->getConfig('honeypot/usehoneypots')) {
            $this->_checkHoneypot();
        }
        if ($HELPER->getConfig('honeypot/enableHoneypotAccountCreateTime')) {
            $this->_checkTimestamp();
        }
        if ($HELPER->getConfig('honeypot/enableSpamIndexing')) {
            $this->_indexLoginParams();
        }
    }
    public function controllerActionPredispatchBlockReviewForm() {
        $HELPER = Mage::helper('wsu_networksecurities');
        if ($HELPER->getConfig('honeypot/usehoneypots')) {
            $this->_checkHoneypot();
        }
    }
    /**
     * validate honeypot field
     */
    protected function _checkHoneypot() {
        $HELPER = Mage::helper('wsu_networksecurities');
        if (strlen(Mage::app()->getRequest()->getParam($HELPER->getConfig('honeypot/honeypotName')))) {
            Mage::log('Honeypot Input filled. Aborted.', Zend_Log::WARN);
            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index', 'error', 'networksecurities');
            throw $e;
        }
    }
    /**
     * validate time
     */
    protected function _checkTimestamp() {
        $session           = Mage::getSingleton('customer/session');
		$HELPER = Mage::helper('wsu_networksecurities');
        $accountCreateTime = $HELPER->getConfig('honeypot/honeypotAccountCreateTime');
        if (!$session->getAccountCreateTime(false) || ($session->getAccountCreateTime() > (time() - $accountCreateTime))) {
            Mage::log('Honeypot Timestamp filled. Aborted.', Zend_Log::WARN);
            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index', 'error', 'networksecurities');
            throw $e;
        }
    }
    /**
     * set access timestamp
     */
    public function controllerActionPredispatchCustomerAccountCreate() {
        $session = Mage::getSingleton('customer/session');
        $session->setAccountCreateTime(time());
    }
    // Invoke indexing
    public function _indexLoginParams() {
		$HELPER = Mage::helper('wsu_networksecurities');
        $checker = Mage::getModel('wsu_networksecurities/checker');
        $return  = $checker->init(Mage::app()->getRequest()->getParams());
        if ($return >= $HELPER->getConfig('honeypot/spamIndexLevel')) {
            Mage::log("Honeypot spam index at $return. Aborted.", Zend_Log::WARN);
            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index', 'error', 'networksecurities');
            throw $e;
        }
    }

	
	
    /**
     * Handler for controller_action_predispatch event
     *
     * @param Varien_Event_Observer $observer
     * @return boolean
     */
    public function actionPreDispatchAdmin($observer) {
        $session             = Mage::getSingleton('admin/session');
        /** @var $session Mage_Admin_Model_Session */
        $request             = Mage::app()->getRequest();
        $user                = $session->getUser();
        $requestedActionName = $request->getActionName();
        $openActions         = array(
            'forgotpassword',
            'resetpassword',
            'resetpasswordpost',
            'requestaccess',
            'requestaccesspost',
            'logout',
            'refresh' // networksecurities refresh
        );
        if (in_array($requestedActionName, $openActions)) {
            $request->setDispatched(true);
        } else {
            if ($user) {
                $user->reload();
            }
            if (!$user || !$user->getId()) {
                if ($request->getPost('login')) {
                    $postLogin = $request->getPost('login');
                    $username  = isset($postLogin['username']) ? $postLogin['username'] : '';
                    $password  = isset($postLogin['password']) ? $postLogin['password'] : '';
                    $session->login($username, $password, $request);
                    $request->setPost('login', null);
                }
                if (!$request->getParam('forwarded')) {
                    if ($request->getParam('isIframe')) {
                        $request->setParam('forwarded', true)->setControllerName('index')->setActionName('deniedIframe')->setDispatched(false);
                    } elseif ($request->getParam('isAjax')) {
                        $request->setParam('forwarded', true)->setControllerName('index')->setActionName('deniedJson')->setDispatched(false);
                    } else {
                        $request->setParam('forwarded', true)->setRouteName('adminhtml')->setControllerName('index')->setActionName('login')->setDispatched(false);
                    }
                    return false;
                }
            }
        }
        $session->refreshAcl();
    }
    /**
     * Check Networksecurities On Forgot Password Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function checkForgotpassword($observer) {
        $formId         = 'user_forgotpassword';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworksecurities($formId);
        if ($networksecuritiesModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$networksecuritiesModel->isCorrect($this->_getNetworksecuritiesString($controller->getRequest(), $formId))) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('wsu_networksecurities')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
            }
        }
        return $this;
    }
    /**
     * Check Networksecurities On User Login Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function checkUserLogin($observer) {
        $formId         = 'user_login';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworksecurities($formId);
        $controller     = $observer->getControllerAction();
        $loginParams    = $controller->getRequest()->getPost('login');
        $login          = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($networksecuritiesModel->isRequired($login)) {
            $word = $this->_getNetworksecuritiesString($controller->getRequest(), $formId);
            if (!$networksecuritiesModel->isCorrect($word)) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('wsu_networksecurities')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setUsername($login);
                $beforeUrl = Mage::getSingleton('customer/session')->getBeforeAuthUrl();
                $url       = $beforeUrl ? $beforeUrl : Mage::helper('customer')->getLoginUrl();
                $controller->getResponse()->setRedirect($url);
            }
        }
        $networksecuritiesModel->logAttempt($login);
        return $this;
    }
    /**
     * Check Networksecurities On Register User Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function checkUserCreate($observer) {
        $formId         = 'user_create';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworksecurities($formId);
        if ($networksecuritiesModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$networksecuritiesModel->isCorrect($this->_getNetworksecuritiesString($controller->getRequest(), $formId))) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('wsu_networksecurities')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/create'));
            }
        }
        return $this;
    }
    /**
     * Check Networksecurities On Checkout as Guest Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function checkGuestCheckout($observer) {
        $formId         = 'guest_checkout';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworksecurities($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_GUEST) {
            if ($networksecuritiesModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$networksecuritiesModel->isCorrect($this->_getNetworksecuritiesString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array(
                        'error' => 1,
                        'message' => Mage::helper('wsu_networksecurities')->__('Incorrect CAPTCHA.')
                    );
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        return $this;
    }
    /**
     * Check Networksecurities On Checkout Register Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function checkRegisterCheckout($observer) {
        $formId         = 'register_during_checkout';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworksecurities($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            if ($networksecuritiesModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$networksecuritiesModel->isCorrect($this->_getNetworksecuritiesString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array(
                        'error' => 1,
                        'message' => Mage::helper('wsu_networksecurities')->__('Incorrect CAPTCHA.')
                    );
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        return $this;
    }
    /**
     * Check Networksecurities On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function checkUserLoginBackend($observer) {
        $formId         = 'backend_login';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworksecurities($formId);
        $loginParams    = Mage::app()->getRequest()->getPost('login', array());
        $login          = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($networksecuritiesModel->isRequired($login)) {
            if (!$networksecuritiesModel->isCorrect($this->_getNetworksecuritiesString(Mage::app()->getRequest(), $formId))) {
                $networksecuritiesModel->logAttempt($login);
                Mage::throwException(Mage::helper('wsu_networksecurities')->__('Incorrect CAPTCHA.'));
            }
        }
        $networksecuritiesModel->logAttempt($login);
        return $this;
    }
    /**
     * Returns backend session
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getBackendSession() {
        return Mage::getSingleton('adminhtml/session');
    }
    /**
     * Check Networksecurities On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function checkUserForgotPasswordBackend($observer) {
        $formId         = 'backend_forgotpassword';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworksecurities($formId);
        $controller     = $observer->getControllerAction();
        $email          = (string) $observer->getControllerAction()->getRequest()->getParam('email');
        $params         = $observer->getControllerAction()->getRequest()->getParams();
        if (!empty($email) && !empty($params)) {
            if ($networksecuritiesModel->isRequired()) {
                if (!$networksecuritiesModel->isCorrect($this->_getNetworksecuritiesString($controller->getRequest(), $formId))) {
                    $this->_getBackendSession()->setEmail((string) $controller->getRequest()->getPost('email'));
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $this->_getBackendSession()->addError(Mage::helper('wsu_networksecurities')->__('Incorrect CAPTCHA.'));
                    $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
                }
            }
        }
        return $this;
    }
    /**
     * Reset Attempts For Frontend
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function resetAttemptForFrontend($observer) {
        return $this->_resetAttempt($observer->getModel()->getEmail());
    }
    /**
     * Reset Attempts For Backend
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function resetAttemptForBackend($observer) {
        return $this->_resetAttempt($observer->getUser()->getUsername());
    }
    /**
     * Delete Unnecessary logged attempts
     *
     * @return Wsu_Networksecurities_Model_Observer
     */
    public function deleteOldAttempts() {
        Mage::getResourceModel('wsu_networksecurities/log')->deleteOldAttempts();
        return $this;
    }

    /**
     * Reset Attempts
     *
     * @param string $login
     * @return Wsu_Networksecurities_Model_Observer
     */
    protected function _resetAttempt($login) {
        Mage::getResourceModel('wsu_networksecurities/log')->deleteUserAttempts($login);
        return $this;
    }
    /**
     * Get Networksecurities String
     *
     * @param Varien_Object $request
     * @param string $formId
     * @return string
     */
    protected function _getNetworksecuritiesString($request, $formId) {
        $networksecuritiesParams = $request->getPost(Wsu_Networksecurities_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $networksecuritiesParams[$formId];
    }
	
	
	// called directed and also from the event admin_session_user_login_failed
	// should be called with the customer too	
	public function setFailedLogin($login,$password=""){
		$pass=($password=="")?$_POST['login']['password']:$password;
		Mage::helper('wsu_networksecurities')->setFailedLogin($login,$pass);
		//Mage::log(Mage::helper('customer')->__('Invalid login or password.'),Zend_Log::WARN);
	}	
	

	

	// called directed and also from the event admin_session_user_login_failed
	// should be called with the customer too	
	public function testBlacklist(){
		$blacklist = Mage::getModel('wsu_networksecurities/blacklist');
		$ip = Mage::helper('wsu_networksecurities')->get_ip_address();
		$status = $blacklist ->getCollection()
			->addFieldToSelect('*')
    		->addFieldToFilter('ip', $ip)
			->getSize();
		if($status>0){
			die('You must contact an admin to get unblocked.  There is no time limit');
		}
		//Mage::log(Mage::helper('customer')->__('Invalid login or password.'),Zend_Log::WARN);
	}	
	
	
}
