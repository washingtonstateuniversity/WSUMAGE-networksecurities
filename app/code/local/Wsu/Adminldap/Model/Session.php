<?php
class Wsu_Adminldap_Model_Session extends Mage_Admin_Model_Session {
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
        /* if (empty($username) || empty($password)) {
        Mage::getSingleton('core/session')->addSuccess('You have loged in.');
        return false;
        }
        */
        if (empty($username) || empty($password)) {
            return;
        }
        $this->load_Parameters();
        if (!$this->actived) //CHECK MAGENTO CONNECT
            return parent::login($username, $password, $request);
        try {
            //print("here");die();exit();
            $this->connect();
            $ldap_user = $this->authentify($username, $password);
            if (!is_a($ldap_user, 'Wsu_Adminldap_Model_Session')) {
                if (!$this->allow_bypass) {
                    Mage::getSingleton('core/session')->addError('Incorrect password our username.<br/> <strong>You now have %s trys before a timeout lock is applied.</strong>');
                    Mage::getSingleton('core/session')->addError('<em>You may not be athourized to use this system to which you must request access.</em>');
                    return false;
                }
                return parent::login($username, $password, $request); //process normally with out ldap
            }
            // Auth SUCCESSFUL
            $user = Mage::getModel('admin/user');
            $user->login($username, $password);
            $logedin = false;
            // Auth SUCCESSFUL on Magento (user & pass match)
            if ($user->getId()) {
                $logedin = true;
            }
            if (!$logedin) {
                $user->load($username, 'username');
                if ($user->getId()) {
                    //User {$username} already exists
                    //lets update the systems password to match LDAP
                    $user->setPassword($password)->save();
                    Mage::getSingleton('core/session')->addSuccess('LDAP Password matched to system.');
                }
            }
            //last check if logged in
            $user->login($username, $password);
            // Auth SUCCESSFUL on Magento (user & pass match)
            if ($user->getId()) { // update user
                $this->renewSession();
                if (Mage::getSingleton('adminhtml/url')->useSecretKey())
                    Mage::getSingleton('adminhtml/url')->renewSecretUrls();
                $this->setIsFirstPageAfterLogin(true);
                $this->setUser($user);
                $this->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                Mage::getSingleton('adminhtml/session')->addNotice("You loged in with LDAP");
                if ($requestUri = $this->_getRequestUri($request)) {
                    Mage::dispatchEvent('admin_session_user_login_success', array(
                        'user' => $user
                    ));
                    header('Location: ' . $requestUri);
                    exit;
                }
            } else { // Does not exist in magento, exists on Ldap
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
                            $user->setUsername($username)->setFirstname($ldap_user->data[0][$this->attr['firstname']][0])->setLastname($ldap_user->data[0][$this->attr['lastname']][0])->setEmail($ldap_user->data[0][$this->attr['mail']][0])->setPassword($password)->save();
                            Mage::getSingleton('core/session')->addSuccess('Password not updated, wrong password');
                        } else {
                            // create user
                            $user = Mage::getModel('admin/user')->setData(array(
                                'username' => $username,
                                'firstname' => $ldap_user->data[0][$this->attr['firstname']][0],
                                'lastname' => $ldap_user->data[0][$this->attr['lastname']][0],
                                'email' => $ldap_user->data[0][$this->attr['mail']][0],
                                'password' => $password,
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
                } else {
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
    private function load_Parameters() {
        //actived 1|0
        $this->actived                 = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/activeldap'));
        $this->allow_bypass            = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/allow_bypass'));
        // 'cn=admin,dc=diva,dc=com';
        $this->rootDn                  = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/rootdn'));
        // '*******'
        $this->rootPassword            = Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/rootpassword');
        //'ou=users,dc=diva,dc=com'
        $this->userDn                  = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/userdn'));
        // '(&(%s=%s)(groups=Wsu-magento-1))';
        $this->filter                  = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/filter'));
        // 'cn';
        $this->cmpAttr                 = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/cmpattr'));
        // 'ldap1'
        $this->host                    = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/host'));
        // '3'
        $this->version                 = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/version')));
        // '389'
        $this->port                    = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/port')));
        // false
        $this->tls                     = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/tls')));
        // cn,givenname,mail,sn,displayname,userpassword
        $this->attr                    = json_decode(trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/attr')), true);
        //default the role_id after each login 0 to disable
        $this->roleId                  = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/defaultroleid')));
        //actived 1|0
        $this->pwdAttr                 = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/passattr'));
        //auto create admin user
        $this->autocreate              = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/autocreate'));
        //user.name
        $this->testusername            = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/testusername'));
        //**password*****
        $this->testuserpass            = trim(Mage::getStoreConfig('wsu_adminldap/ldapadminlogin/testuserpass'));
        $this->searcherrootDn          = trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/rootdn'));
        // '*******'
        $this->searcherrootPassword    = Mage::getStoreConfig('wsu_adminldap/ldapsearcher/rootpassword');
        //'ou=users,dc=diva,dc=com'
        $this->searcheruserDn          = trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/userdn'));
        // '(&(%s=%s)(groups=Wsu-magento-1))';
        $this->searcherfilter          = trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/filter'));
        // 'cn';
        $this->searchercmpAttr         = trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/cmpattr'));
        // 'ldap1'
        $this->searcherhost            = trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/host'));
        // '3'
        $this->searcherversion         = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/version')));
        // '389'
        $this->searcherport            = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/port')));
        // false
        $this->searchertls             = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/tls')));
        // cn,givenname,mail,sn,displayname,userpassword
        $this->searcherattr            = json_decode(trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/attr')), true);
        //default the role_id after each login 0 to disable
        $this->searcherroleId          = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/defaultroleid')));
        //actived 1|0
        $this->searcherpwdAttr         = trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/passattr'));
        //actived 1|0
        $this->searcheractived         = trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/activeldap'));
        $this->searcherusername        = trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/searcherusername'));
        //**password*****
        $this->searcheruserpass        = trim(Mage::getStoreConfig('wsu_adminldap/ldapsearcher/searcheruserpass'));
        //actived 1|0
        $this->customer_actived        = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/activeldap'));
        //actived 1|0
        $this->customer_restricttoldap = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/restricttoldap'));
        // 'cn=admin,dc=diva,dc=com';
        $this->customer_rootDn         = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/rootdn'));
        // '*******'
        $this->customer_rootPassword   = Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/rootpassword');
        //'ou=users,dc=diva,dc=com'
        $this->customer_userDn         = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/userdn'));
        // '(&(%s=%s)(groups=Wsu-magento-1))';
        $this->customer_filter         = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/filter'));
        // 'cn';
        $this->customer_cmpAttr        = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/cmpattr'));
        // 'ldap1'
        $this->customer_host           = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/host'));
        // '3'
        $this->customer_version        = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/version')));
        // '389'
        $this->customer_port           = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/port')));
        // false
        $this->customer_tls            = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/tls')));
        // cn,givenname,mail,sn,displayname,userpassword
        $this->customer_attr           = json_decode(trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/attr')), true);
        //default the role_id after each login 0 to disable
        $this->customer_roleId         = intval(trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/defaultroleid')));
        //actived 1|0
        $this->customer_pwdAttr        = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/passattr'));
        //auto create admin user
        $this->customer_autocreate     = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/autocreate'));
        //user.name
        $this->testusername            = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/testusername'));
        //**password*****
        $this->testuserpass            = trim(Mage::getStoreConfig('wsu_adminldap/ldapcustomerlogin/testuserpass'));
    }
    private function connect() {
        $this->load_Parameters();
        if (is_null(self::$ldaplink)) {
            if ($this->tls)
                $url = 'ldaps://' . $this->host . '/';
            else
                $url = 'ldap://' . $this->host . '/';
            self::$ldaplink = ldap_connect($url, $this->port) or die("Could not connect to $ldaphost");
        }
        if (!ldap_set_option(self::$ldaplink, LDAP_OPT_PROTOCOL_VERSION, $this->version)) {
            Mage::getSingleton('adminhtml/session')->addError("Wsu" . ldap_errno(self::$ldaplink));
        }
        //die('AUTH_ADMIN ERROR : VERSION ERROR');
        if (!ldap_set_option(self::$ldaplink, LDAP_OPT_REFERRALS, 0)) {
            Mage::getSingleton('adminhtml/session')->addError("Wsu" . ldap_errno(self::$ldaplink));
        }
        //die('AUTH_ADMIN ERROR : VERSION ERROR');
        if ($this->rootDn == "")
            $this->rootDn = null;
        if ($this->rootPassword == "")
            $this->rootPassword = null;
			
			
		$ldaped = @ldap_bind(self::$ldaplink, $this->rootDn, $this->rootPassword);
        if (!$ldaped) {
            Mage::getSingleton('adminhtml/session')->addError("Wsu" . ldap_errno(self::$ldaplink));
        }
        if (self::$ldaplink) {
        } else {
            echo "Unable to connect to LDAP server";
            die();
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
            if (!$data)
                throw new Exception('AUTH_ADMIN ERROR : SEARCH ERROR');
            $this->data = ldap_get_entries($ds, $data);
            if ($this->data['count'] != 1)
                return false;
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
            $ldap_usr_dom = "@wsu.edu"; //fix this fool
            //$r=ldap_bind( $ds, $dn, $password );
            //$r=ldap_compare($ds, $dn, $attr, $value);
            $ldap         = self::$ldaplink;
            $r            = ldap_bind($ldap, $login . $ldap_usr_dom, $password);
            if ($r === -1) {
                $params = $login . " -- " . $password;
                Mage::getSingleton('core/session')->addError(ldap_error($r));
            } elseif ($r === true) {
                if ($this->is_Allowed($login))
                    return $this;
            } elseif ($r === false) {
                //error message to be passed later
            }
            return false;
        }
        catch (Exception $e) {
            //Mage::getSingleton('core/session')->addError("Error: " . $e);
            return false;
        }
    }
    public function get_Ldap_User_Attributs() {
        foreach ($this->attr as $attr)
            $ret[$attr] = $this->data[0][$attr][0];
        return $ret;
    }
}