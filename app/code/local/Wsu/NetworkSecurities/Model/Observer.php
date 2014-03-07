<?php
/**
 * Admin observer model
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Wsu_NetworkSecurities_Model_Observer extends Mage_Admin_Model_Observer {
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
     * Check NetworkSecurities On Forgot Password Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function checkForgotpassword($observer) {
        $formId         = 'user_forgotpassword';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworkSecurities($formId);
        if ($networksecuritiesModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$networksecuritiesModel->isCorrect($this->_getNetworkSecuritiesString($controller->getRequest(), $formId))) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('wsu_networksecurities')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
            }
        }
        return $this;
    }
    /**
     * Check NetworkSecurities On User Login Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function checkUserLogin($observer) {
        $formId         = 'user_login';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworkSecurities($formId);
        $controller     = $observer->getControllerAction();
        $loginParams    = $controller->getRequest()->getPost('login');
        $login          = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($networksecuritiesModel->isRequired($login)) {
            $word = $this->_getNetworkSecuritiesString($controller->getRequest(), $formId);
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
     * Check NetworkSecurities On Register User Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function checkUserCreate($observer) {
        $formId         = 'user_create';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworkSecurities($formId);
        if ($networksecuritiesModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$networksecuritiesModel->isCorrect($this->_getNetworkSecuritiesString($controller->getRequest(), $formId))) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('wsu_networksecurities')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/create'));
            }
        }
        return $this;
    }
    /**
     * Check NetworkSecurities On Checkout as Guest Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function checkGuestCheckout($observer) {
        $formId         = 'guest_checkout';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworkSecurities($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_GUEST) {
            if ($networksecuritiesModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$networksecuritiesModel->isCorrect($this->_getNetworkSecuritiesString($controller->getRequest(), $formId))) {
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
     * Check NetworkSecurities On Checkout Register Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function checkRegisterCheckout($observer) {
        $formId         = 'register_during_checkout';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworkSecurities($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            if ($networksecuritiesModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$networksecuritiesModel->isCorrect($this->_getNetworkSecuritiesString($controller->getRequest(), $formId))) {
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
     * Check NetworkSecurities On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function checkUserLoginBackend($observer) {
        $formId         = 'backend_login';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworkSecurities($formId);
        $loginParams    = Mage::app()->getRequest()->getPost('login', array());
        $login          = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($networksecuritiesModel->isRequired($login)) {
            if (!$networksecuritiesModel->isCorrect($this->_getNetworkSecuritiesString(Mage::app()->getRequest(), $formId))) {
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
     * Check NetworkSecurities On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function checkUserForgotPasswordBackend($observer) {
        $formId         = 'backend_forgotpassword';
        $networksecuritiesModel = Mage::helper('wsu_networksecurities')->getNetworkSecurities($formId);
        $controller     = $observer->getControllerAction();
        $email          = (string) $observer->getControllerAction()->getRequest()->getParam('email');
        $params         = $observer->getControllerAction()->getRequest()->getParams();
        if (!empty($email) && !empty($params)) {
            if ($networksecuritiesModel->isRequired()) {
                if (!$networksecuritiesModel->isCorrect($this->_getNetworkSecuritiesString($controller->getRequest(), $formId))) {
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
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function resetAttemptForFrontend($observer) {
        return $this->_resetAttempt($observer->getModel()->getEmail());
    }
    /**
     * Reset Attempts For Backend
     *
     * @param Varien_Event_Observer $observer
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function resetAttemptForBackend($observer) {
        return $this->_resetAttempt($observer->getUser()->getUsername());
    }
    /**
     * Delete Unnecessary logged attempts
     *
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    public function deleteOldAttempts() {
        Mage::getResourceModel('wsu_networksecurities/log')->deleteOldAttempts();
        return $this;
    }

    /**
     * Reset Attempts
     *
     * @param string $login
     * @return Wsu_NetworkSecurities_Model_Observer
     */
    protected function _resetAttempt($login) {
        Mage::getResourceModel('wsu_networksecurities/log')->deleteUserAttempts($login);
        return $this;
    }
    /**
     * Get NetworkSecurities String
     *
     * @param Varien_Object $request
     * @param string $formId
     * @return string
     */
    protected function _getNetworkSecuritiesString($request, $formId) {
        $networksecuritiesParams = $request->getPost(Wsu_NetworkSecurities_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $networksecuritiesParams[$formId];
    }
	
	
	
	
	// called directed and also from the event admin_session_user_login_failed
	// should be called with the customer too	
	public function setFailedLogin($login,$password=""){
		$failed_log = Mage::getModel('wsu_networksecurities/failedlogin');
		
		//$pastatempts->addFieldToFilter('ip',$_SERVER['REMOTE_ADDR']);
		if(is_object($login)){
			$login=$login->getUsername();	
		}
		if($login==null){
			if(isset($_POST['login'])){
				$login=$_POST['login']['username'];
			}
		}
		$ip = Mage::helper('wsu_networksecurities')->get_ip_address();
		$failed_log->setLogin($login);
		$failed_log->setPassword(md5($password));//note this must not be use for more then just a check that they may have forgot the pass
		$failed_log->setIp($ip);
		$failed_log->setUserAgent($_SERVER['HTTP_USER_AGENT']);
		$failed_log->setAdmin(Mage::app()->getStore()->isAdmin());
		$failed_log->save();
		$cookie = Mage::getSingleton('core/cookie');
		$count=1;
		if(isset($_COOKIE['userpasshash'])){
			$old=explode(':',$_COOKIE['userpasshash']);
			$count=(int)end($old)+1;
		}
		#this is to send wouldbe level hackers on a runaround
		$cookie->set('userpasshash', md5(time()).":".$count ,time()+86400,'/');
		$pastatempts = $failed_log ->getCollection()
			->addFieldToSelect('*')
    		->addFieldToFilter('ip', $ip)
			->getSize();
		//var_dump($pastatempts);die();
		if($pastatempts>3){
			$this->setBlacklist($ip);
		}
		//Mage::log(Mage::helper('customer')->__('Invalid login or password.'),Zend_Log::WARN);
	}
	
	// called directed and also from the event admin_session_user_login_failed
	// should be called with the customer too	
	public function setBlacklist($ip){
		$blacklist = Mage::getModel('wsu_networksecurities/blacklist');
		$ip = Mage::helper('wsu_networksecurities')->get_ip_address();
		$blacklist->setIp($ip);
		$blacklist->setAdmin(Mage::app()->getStore()->isAdmin());
		$blacklist->save();
		$cookie = Mage::getSingleton('core/cookie');
		$count=1;
		if(isset($_COOKIE['userBLhash'])){
			$old=explode(':',$_COOKIE['userBLhash']);
			$count=(int)end($old)+1;
		}
		#this is to send wouldbe level hackers on a runaround
		$cookie->set('userBLhash', md5(time()).":".$count ,time()+86400,'/');
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
		if($status>1){
			die('You must contact an admin to get unblocked.  There is no time limit');
		}
		//Mage::log(Mage::helper('customer')->__('Invalid login or password.'),Zend_Log::WARN);
	}	
	
	
}
