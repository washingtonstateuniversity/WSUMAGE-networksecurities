<?php
class Wsu_Networksecurities_Model_Session extends Mage_Admin_Model_Session {
    //@todo this si the object conversion to param
    protected static $ldaplink = null;
    public $host;
    public $version;
    public $rootDn;
    public $rootPassword;
    public $userDn;
    public $filter;
    public $cmpAttr;
    public $pwdAttr;
    public $attr;
    public $tls;
    public $roleId;
    public $actived;
    public $data = array();
    public $allow_bypass;
    protected static $searcherldaplink = null;
    public $searcherhost;
    public $searcherversion;
    public $searcherrootDn;
    public $searcherrootPassword;
    public $searcheruserDn;
    public $searcherfilter;
    public $searchercmpAttr;
    public $searcherpwdAttr;
    public $searcherattr;
    public $searchertls;
    public $searcherroleId;
    public $searcheractived;
    /*
     * Override admin login
     */
    public function login($username, $password, $request = null) {
		$helper = Mage::helper('wsu_networksecurities');

		if(!$helper->testLogin($username,$password))
			return;

        $this->load_Parameters();
        if (!$this->actived) //CHECK MAGENTO CONNECT
            return parent::login($username, $password, $request);
			
        try {
			Mage::helper('wsu_networksecurities')->log("using Ldap",Zend_Log::NOTICE);
            //print("here");die();exit();
            $this->connect();
            $ldap_user = $this->authentify($username, $password);
			$ldappass=false;
            if (!is_a($ldap_user, 'Wsu_Networksecurities_Model_Session')) {
				Mage::helper('wsu_networksecurities')->log("not a Ldap user",Zend_Log::NOTICE);
                if (!$this->allow_bypass) {
					Mage::helper('wsu_networksecurities')->log("not able to bypass Ldap",Zend_Log::NOTICE);
					
                    Mage::getSingleton('core/session')->addError('Incorrect password our username.<br/> <strong>You now have %s trys before a timeout lock is applied.</strong>');
                    Mage::getSingleton('core/session')->addError('<em>You may not be athourized to use this system to which you must request access.</em>');
					Mage::helper('wsu_networksecurities')->setFailedLogin($login,$password);
                    return false;
                }
                return parent::login($username, $password, $request); //process normally with out ldap
            }
			Mage::helper('wsu_networksecurities')->log("passed Ldap",Zend_Log::NOTICE);
			//it is assumed that if you are here that you have passes ldap
            // Auth SUCCESSFUL
			
			
            $user = Mage::getModel('admin/user');
            $user->login($username, $password);
            $logedin = ($user->getId())?true:false;
            

            if (!$logedin) {
				Mage::helper('wsu_networksecurities')->log("passed Ldap but wasn't logedin",Zend_Log::NOTICE);
                $exitsinguser = $user->load($username, 'username');
                if ($exitsinguser->getId()) {
                    //User {$username} already exists
                    //lets update the systems password to match LDAP
					if( $exitsinguser->getPassword() != $password ) {
                    	$exitsinguser->setNewPassword($password);
						$exitsinguser->setPasswordConfirmation($password);
						$exitsinguser->save();
						Mage::helper('wsu_networksecurities')->log("saved new LDAP password",Zend_Log::NOTICE);
					}
					$exitsinguser->setLdapUser(1)->save();
                }
				$user->login($username, $password);
				if ($user->getId()) {
					$logedin = true;
				}
            }

            if ($logedin) { 
				// Auth SUCCESSFUL on Magento (user & pass match)
				Mage::helper('wsu_networksecurities')->log("User passed ldap and was logged in",Zend_Log::NOTICE);
                $this->renewSession();
                if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
                    Mage::getSingleton('adminhtml/url')->renewSecretUrls();
				}
                $this->setIsFirstPageAfterLogin(true);
                $this->setUser($user);
				
                $this->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                Mage::getSingleton('adminhtml/session')->addNotice("You loged in with LDAP");
				$user->setLdapUser(1)->save(); // this may be redunent after the first time loggin in but a check is neededless as it's just more time.. maybe .. 
				
                if ($requestUri = $this->_getRequestUri($request)) {
                    Mage::dispatchEvent('admin_session_user_login_success', array(
                        'user' => $user
                    ));
                    header('Location: ' . $requestUri);
                    exit;
                }
            }else{ // Does not exist in magento, exists on Ldap
                if ($this->autocreate) {
                    try {
                        $exist = false;
                        //$admin->loadByEmail($email);
                        // test if a user already exists (check username)
                        $users = Mage::getModel('admin/user')->getCollection()->getData();
                        foreach ($users as $userData => $val) {
                            if ($val['username'] == $username)
                                $exist = true;
                        }
                        if ($exist) { // update user
                            $user = Mage::getModel('admin/user')->load($val['user_id']);
                            $user->setUsername($username)->setFirstname($ldap_user->data[0][$this->attr['firstname']][0])->setLastname($ldap_user->data[0][$this->attr['lastname']][0])->setEmail($ldap_user->data[0][$this->attr['mail']][0])->setPassword($password)->setLdapUser(1)->save();
                            Mage::getSingleton('core/session')->addSuccess('Password not updated, wrong password');
                        }else{ // create user
                            $user = Mage::getModel('admin/user')->setData(array(
                                'username' => $username,
                                'firstname' => $ldap_user->data[0][$this->attr['firstname']][0],
                                'lastname' => $ldap_user->data[0][$this->attr['lastname']][0],
                                'email' => $ldap_user->data[0][$this->attr['mail']][0],
                                'password' => $password,
								'ldap_user' => 1,
                                'is_active' => 1
                            ))->save();
                            Mage::getSingleton('core/session')->addSuccess('User created on');
                            $user->setRoleIds(array(
                                $this->roleId
                            ))->setRoleUserId($user->getUserId())->saveRelations();
                        }
                        // alter session
                        $user->login($username, $password);
                        $this->renewSession();
                        if (Mage::getSingleton('adminhtml/url')->useSecretKey())
                            Mage::getSingleton('adminhtml/url')->renewSecretUrls();
                        $this->setIsFirstPageAfterLogin(true);
                        $this->setUser($user);
                        $this->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                        if ($requestUri = $this->_getRequestUri($request)) {
                            Mage::dispatchEvent('admin_session_user_login_success', array(
                                'user' => $user
                            ));
                            header('Location: ' . $requestUri);
                            exit;
                        }
                    }
                    catch (Exception $e) {
                        echo $e->getMessage();
                        exit;
                    }
                }else{ Mage::helper('wsu_networksecurities')->setFailedLogin($username,$password);
                    Mage::getSingleton('core/session')->addError('You may not be athourized to use this system. You must contact an admin to be given rights');
                    return false;
                }
            }
        }
        catch (Mage_Core_Exception $e) {
            Mage::dispatchEvent('admin_session_user_login_failed', array(
                'user_name' => $username,
                'exception' => $e
            ));
            if ($request && !$request->getParam('messageSent')) {
                Mage::getSingleton('adminhtml/session')->addError("Wsu" . $e->getMessage());
                $request->setParam('messageSent', true);
            }
        }
        return $user;
    }
	
	//@todo swtich to the object
	//Mage::helper('adminhtml')->getConfig('adminlogin/activeldap')
    private function load_Parameters() {
		$HELPER = Mage::helper('wsu_networksecurities');

        //admin
        $this->actived                 = $HELPER->getConfig('adminlogin/activeldap');// 1|0
        $this->allow_bypass            = $HELPER->getConfig('adminlogin/allow_bypass');// 1|0
        $this->rootDn                  = $HELPER->getConfig('adminlogin/rootdn');// 'cn=admin,dc=wsu,dc=com';
        $this->rootPassword            = $HELPER->getConfig('adminlogin/rootpassword');// '*******'
        $this->userDn                  = $HELPER->getConfig('adminlogin/userdn');//'ou=users,dc=wsu,dc=com'
        $this->filter                  = $HELPER->getConfig('adminlogin/filter');// '(&(%s=%s)(groups=Wsu-magento-1))';
        $this->cmpAttr                 = $HELPER->getConfig('adminlogin/cmpattr');// 'cn';
        $this->host                    = $HELPER->getConfig('adminlogin/host');// 'ldap1'
        $this->version                 = intval($HELPER->getConfig('adminlogin/version'));// '3'
        $this->port                    = intval($HELPER->getConfig('adminlogin/port'));// '389'
        $this->tls                     = intval($HELPER->getConfig('adminlogin/tls'));// false
        $this->attr                    = json_decode($HELPER->getConfig('adminlogin/attr'), true);// cn,givenname,mail,sn,displayname,userpassword 
        $this->roleId                  = intval($HELPER->getConfig('adminlogin/defaultroleid'));//default the role_id after each login 0 to disable
        $this->pwdAttr                 = $HELPER->getConfig('adminlogin/passattr');//password
        $this->autocreate              = $HELPER->getConfig('adminlogin/autocreate');//1|0
        $this->testusername            = $HELPER->getConfig('adminlogin/testusername');//user.name 
        $this->testuserpass            = $HELPER->getConfig('adminlogin/testuserpass');//**password*****
		$this->ldap_usr_dom            = $HELPER->getConfig('adminlogin/ldap_usr_dom');//@wsu.edu
		//seracher
        $this->searcherrootDn          = $HELPER->getConfig('searcher/rootdn');
        $this->searcherrootPassword    = $HELPER->getConfig('searcher/rootpassword');
        $this->searcheruserDn          = $HELPER->getConfig('searcher/userdn');
        $this->searcherfilter          = $HELPER->getConfig('searcher/filter');
        $this->searchercmpAttr         = $HELPER->getConfig('searcher/cmpattr');
        $this->searcherhost            = $HELPER->getConfig('searcher/host');
        $this->searcherversion         = intval($HELPER->getConfig('searcher/version'));
        $this->searcherport            = intval($HELPER->getConfig('searcher/port'));
        $this->searchertls             = intval($HELPER->getConfig('searcher/tls'));
        $this->searcherattr            = json_decode($HELPER->getConfig('searcher/attr'), true);
        $this->searcherroleId          = intval($HELPER->getConfig('searcher/defaultroleid'));
        $this->searcherpwdAttr         = $HELPER->getConfig('searcher/passattr');
        $this->searcheractived         = $HELPER->getConfig('searcher/activeldap');
        $this->searcherusername        = $HELPER->getConfig('searcher/searcherusername');
        $this->searcheruserpass        = $HELPER->getConfig('searcher/searcheruserpass');
		$this->searcherldap_usr_dom    = $HELPER->getConfig('searcher/ldap_usr_dom');
		//customer
        $this->customer_actived        = $HELPER->getConfig('customerlogin/activeldap');
        $this->customer_restricttoldap = $HELPER->getConfig('customerlogin/restricttoldap');
        $this->customer_rootDn         = $HELPER->getConfig('customerlogin/rootdn');
        $this->customer_rootPassword   = $HELPER->getConfig('customerlogin/rootpassword');
        $this->customer_userDn         = $HELPER->getConfig('customerlogin/userdn');
        $this->customer_filter         = $HELPER->getConfig('customerlogin/filter');
        $this->customer_cmpAttr        = $HELPER->getConfig('customerlogin/cmpattr');
        $this->customer_host           = $HELPER->getConfig('customerlogin/host');
        $this->customer_version        = intval($HELPER->getConfig('customerlogin/version'));
        $this->customer_port           = intval($HELPER->getConfig('customerlogin/port'));
        $this->customer_tls            = intval($HELPER->getConfig('customerlogin/tls'));
        $this->customer_attr           = json_decode($HELPER->getConfig('customerlogin/attr'), true);
        $this->customer_roleId         = intval($HELPER->getConfig('customerlogin/defaultroleid'));
        $this->customer_pwdAttr        = $HELPER->getConfig('customerlogin/passattr');
        $this->customer_autocreate     = $HELPER->getConfig('customerlogin/autocreate');
        $this->customer_testusername   = $HELPER->getConfig('customerlogin/testusername');
        $this->customer_testuserpass   = $HELPER->getConfig('customerlogin/testuserpass');
		$this->customerldap_usr_dom    = $HELPER->getConfig('customerlogin/ldap_usr_dom');
    }
    private function connect() {
        $this->load_Parameters();
        if (is_null(self::$ldaplink)) {
            if ($this->tls)
                $url = 'ldaps://' . $this->host . '/';
            else
                $url = 'ldap://' . $this->host . '/';
            self::$ldaplink = ldap_connect($url, $this->port) or Mage::app()->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')->setBody("Could not connect to $ldaphost");
        }
        if (!ldap_set_option(self::$ldaplink, LDAP_OPT_PROTOCOL_VERSION, $this->version)) {
			$err=ldap_errno(self::$ldaplink);
			Mage::log($err,Zend_Log::ERROR,"adminlog.txt");
            Mage::getSingleton('adminhtml/session')->addError($err);
        }
        //die('AUTH_ADMIN ERROR : VERSION ERROR');
        if (!ldap_set_option(self::$ldaplink, LDAP_OPT_REFERRALS, 0)) {
			$err=ldap_errno(self::$ldaplink);
			Mage::log($err,Zend_Log::ERROR,"adminlog.txt");
            Mage::getSingleton('adminhtml/session')->addError($err);
        }
        //die('AUTH_ADMIN ERROR : VERSION ERROR');
        if ($this->rootDn == "")
            $this->rootDn = null;
        if ($this->rootPassword == "")
            $this->rootPassword = null;
			
			
		$ldaped = @ldap_bind(self::$ldaplink, $this->rootDn, $this->rootPassword);
        if (!$ldaped) {
			$err=ldap_errno(self::$ldaplink);
			Mage::helper('wsu_networksecurities')->log($err,Zend_Log::ERROR);
        }
        if (self::$ldaplink) {
        }else{ //echo "Unable to connect to LDAP server";
			Mage::log("Unable to connect to LDAP server",Zend_Log::ERROR,"adminlog.txt");
            Mage::app()->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/html')
					->setBody("Unable to connect to LDAP server");
        }
        //die('AUTH_ADMIN ERROR : BIND ERROR');
    }
    public function get_Link() {
        if (empty(self::$ldaplink))
            $this->connect();
        return self::$ldaplink;
    }
    public function is_Allowed($login) {
        if ($this->filter != "") {
            $filter = sprintf($this->filter, $this->cmpAttr, $login);
            $userDn = $this->cmpAttr . '=' . $login . ',' . $this->userDn;
            $ds     = $this->get_Link();
            $data   = ldap_search($ds, $userDn, $filter, array_values($this->attr));
            if (!$data){
                throw new Exception('AUTH_ADMIN ERROR : SEARCH ERROR');
			}
            $this->data = ldap_get_entries($ds, $data);
            if ($this->data['count'] != 1){
                return false;
			}
            return true;
        }
        return true;
    }
    public function authentify($login = null, $password = null) {
        if (is_null($login) || is_null($password))
            return false;
        $ds    = $this->get_Link();
        //$login=$dn = $this->cmpAttr.'='.$login.','.$this->userDn;
        $attr  = $this->pwdAttr;
        $value = $password;
        try {
            $ldap_usr_dom = $this->ldap_usr_dom;//"@wsu.edu"; //fix this fool
            //$r=ldap_bind( $ds, $dn, $password );
            //$r=ldap_compare($ds, $dn, $attr, $value);
            $ldap         = self::$ldaplink;
            $r            = ldap_bind($ldap, $login . $ldap_usr_dom, $password);
            if ($r === -1) {
                $params = $login . " -- " . $password;
				
				$err=ldap_error($r);
				Mage::helper('wsu_networksecurities')->log($err,Zend_Log::ERROR);
            }elseif ($r === true) {
                if ($this->is_Allowed($login)) {
                    return $this;
				}
            }elseif ($r === false) {
                //error message to be passed later
            }
            return false;
        }
        catch (Exception $e) {
			Mage::helper('wsu_networksecurities')->log($e,Zend_Log::ERROR);
            return false;
        }
    }
    public function get_Ldap_User_Attributs() {
        foreach ($this->attr as $attr){
            $ret[$attr] = $this->data[0][$attr][0];
		}
        return $ret;
    }
}
