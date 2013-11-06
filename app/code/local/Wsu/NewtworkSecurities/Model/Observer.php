<?php
/**
 * Admin observer model
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Wsu_NewtworkSecurities_Model_Observer extends Mage_Admin_Model_Observer {
    const FLAG_NO_LOGIN = 'no-login';

    /**
     * call rules
     */
    public function controllerActionPredispatchCustomerAccountCreatepost() {
		$HELPER = Mage::helper('wsu_newtworksecurities');
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
        $HELPER = Mage::helper('wsu_newtworksecurities');
        if ($HELPER->getConfig('honeypot/usehoneypots')) {
            $this->_checkHoneypot();
        }
    }
    /**
     * validate honeypot field
     */
    protected function _checkHoneypot() {
        $HELPER = Mage::helper('wsu_newtworksecurities');
        if (strlen(Mage::app()->getRequest()->getParam($HELPER->getConfig('honeypot/honeypotName')))) {
            Mage::log('Honeypot Input filled. Aborted.', Zend_Log::WARN);
            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index', 'error', 'newtworksecurities');
            throw $e;
        }
    }
    /**
     * validate time
     */
    protected function _checkTimestamp() {
        $session           = Mage::getSingleton('customer/session');
		$HELPER = Mage::helper('wsu_newtworksecurities');
        $accountCreateTime = $HELPER->getConfig('honeypot/honeypotAccountCreateTime');
        if (!$session->getAccountCreateTime(false) || ($session->getAccountCreateTime() > (time() - $accountCreateTime))) {
            Mage::log('Honeypot Timestamp filled. Aborted.', Zend_Log::WARN);
            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index', 'error', 'newtworksecurities');
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
		$HELPER = Mage::helper('wsu_newtworksecurities');
        $checker = Mage::getModel('wsu_newtworksecurities/checker');
        $return  = $checker->init(Mage::app()->getRequest()->getParams());
        if ($return >= $HELPER->getConfig('honeypot/spamIndexLevel')) {
            Mage::log("Honeypot spam index at $return. Aborted.", Zend_Log::WARN);
            $e = new Mage_Core_Controller_Varien_Exception();
            $e->prepareForward('index', 'error', 'newtworksecurities');
            throw $e;
        }
    }
	
	public function appendHoneypot()
    {
        echo $this->getLayout()->createBlock('wsu_newtworksecurities/newtworksecurities.honeypot')->toHtml();
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
            'refresh' // newtworksecurities refresh
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
     * Check NewtworkSecurities On Forgot Password Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function checkForgotpassword($observer) {
        $formId         = 'user_forgotpassword';
        $newtworksecuritiesModel = Mage::helper('newtworksecurities')->getNewtworkSecurities($formId);
        if ($newtworksecuritiesModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$newtworksecuritiesModel->isCorrect($this->_getNewtworkSecuritiesString($controller->getRequest(), $formId))) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('newtworksecurities')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
            }
        }
        return $this;
    }
    /**
     * Check NewtworkSecurities On User Login Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function checkUserLogin($observer) {
        $formId         = 'user_login';
        $newtworksecuritiesModel = Mage::helper('newtworksecurities')->getNewtworkSecurities($formId);
        $controller     = $observer->getControllerAction();
        $loginParams    = $controller->getRequest()->getPost('login');
        $login          = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($newtworksecuritiesModel->isRequired($login)) {
            $word = $this->_getNewtworkSecuritiesString($controller->getRequest(), $formId);
            if (!$newtworksecuritiesModel->isCorrect($word)) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('newtworksecurities')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setUsername($login);
                $beforeUrl = Mage::getSingleton('customer/session')->getBeforeAuthUrl();
                $url       = $beforeUrl ? $beforeUrl : Mage::helper('customer')->getLoginUrl();
                $controller->getResponse()->setRedirect($url);
            }
        }
        $newtworksecuritiesModel->logAttempt($login);
        return $this;
    }
    /**
     * Check NewtworkSecurities On Register User Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function checkUserCreate($observer) {
        $formId         = 'user_create';
        $newtworksecuritiesModel = Mage::helper('newtworksecurities')->getNewtworkSecurities($formId);
        if ($newtworksecuritiesModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$newtworksecuritiesModel->isCorrect($this->_getNewtworkSecuritiesString($controller->getRequest(), $formId))) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('newtworksecurities')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/create'));
            }
        }
        return $this;
    }
    /**
     * Check NewtworkSecurities On Checkout as Guest Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function checkGuestCheckout($observer) {
        $formId         = 'guest_checkout';
        $newtworksecuritiesModel = Mage::helper('newtworksecurities')->getNewtworkSecurities($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_GUEST) {
            if ($newtworksecuritiesModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$newtworksecuritiesModel->isCorrect($this->_getNewtworkSecuritiesString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array(
                        'error' => 1,
                        'message' => Mage::helper('newtworksecurities')->__('Incorrect CAPTCHA.')
                    );
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        return $this;
    }
    /**
     * Check NewtworkSecurities On Checkout Register Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function checkRegisterCheckout($observer) {
        $formId         = 'register_during_checkout';
        $newtworksecuritiesModel = Mage::helper('newtworksecurities')->getNewtworkSecurities($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            if ($newtworksecuritiesModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$newtworksecuritiesModel->isCorrect($this->_getNewtworkSecuritiesString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array(
                        'error' => 1,
                        'message' => Mage::helper('newtworksecurities')->__('Incorrect CAPTCHA.')
                    );
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        return $this;
    }
    /**
     * Check NewtworkSecurities On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function checkUserLoginBackend($observer) {
        $formId         = 'backend_login';
        $newtworksecuritiesModel = Mage::helper('newtworksecurities')->getNewtworkSecurities($formId);
        $loginParams    = Mage::app()->getRequest()->getPost('login', array());
        $login          = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($newtworksecuritiesModel->isRequired($login)) {
            if (!$newtworksecuritiesModel->isCorrect($this->_getNewtworkSecuritiesString(Mage::app()->getRequest(), $formId))) {
                $newtworksecuritiesModel->logAttempt($login);
                Mage::throwException(Mage::helper('newtworksecurities')->__('Incorrect CAPTCHA.'));
            }
        }
        $newtworksecuritiesModel->logAttempt($login);
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
     * Check NewtworkSecurities On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function checkUserForgotPasswordBackend($observer) {
        $formId         = 'backend_forgotpassword';
        $newtworksecuritiesModel = Mage::helper('newtworksecurities')->getNewtworkSecurities($formId);
        $controller     = $observer->getControllerAction();
        $email          = (string) $observer->getControllerAction()->getRequest()->getParam('email');
        $params         = $observer->getControllerAction()->getRequest()->getParams();
        if (!empty($email) && !empty($params)) {
            if ($newtworksecuritiesModel->isRequired()) {
                if (!$newtworksecuritiesModel->isCorrect($this->_getNewtworkSecuritiesString($controller->getRequest(), $formId))) {
                    $this->_getBackendSession()->setEmail((string) $controller->getRequest()->getPost('email'));
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $this->_getBackendSession()->addError(Mage::helper('newtworksecurities')->__('Incorrect CAPTCHA.'));
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
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function resetAttemptForFrontend($observer) {
        return $this->_resetAttempt($observer->getModel()->getEmail());
    }
    /**
     * Reset Attempts For Backend
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function resetAttemptForBackend($observer) {
        return $this->_resetAttempt($observer->getUser()->getUsername());
    }
    /**
     * Delete Unnecessary logged attempts
     *
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    public function deleteOldAttempts() {
        Mage::getResourceModel('newtworksecurities/log')->deleteOldAttempts();
        return $this;
    }

    /**
     * Reset Attempts
     *
     * @param string $login
     * @return Wsu_NewtworkSecurities_Model_Observer
     */
    protected function _resetAttempt($login) {
        Mage::getResourceModel('newtworksecurities/log')->deleteUserAttempts($login);
        return $this;
    }
    /**
     * Get NewtworkSecurities String
     *
     * @param Varien_Object $request
     * @param string $formId
     * @return string
     */
    protected function _getNewtworkSecuritiesString($request, $formId) {
        $newtworksecuritiesParams = $request->getPost(Wsu_NewtworkSecurities_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $newtworksecuritiesParams[$formId];
    }
}
