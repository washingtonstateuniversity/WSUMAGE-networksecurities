<?php
/*

flow --
-check LDAP if on
-if LDAP off pass through
- if on and pass LDAP, check MAGE for user
---if user not in MAGE alert to signup
--if LDAP fail, check with out LDAP
--if neither alert to signup



*/
class Wsu_NewtworkSecurities_Model_Customer_Session extends Mage_Customer_Model_Session {

	//pare this down
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
	
    public $customer_host;
    public $customer_version;
    public $customer_rootDn;
    public $customer_rootPassword;
    public $customer_userDn;
    public $customer_filter;
    public $customer_cmpAttr;
    public $customer_pwdAttr;
    public $customer_attr;
    public $customer_tls;
    public $customer_roleId;
    public $customer_actived;

    /**
     * Customer authorization
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function login($username, $password){
		
		$this->load_Parameters();

		
		if (!$this->customer_actived) //CHECK MAGENTO CONNECT
			return parent::login($username, $password);

        try { 	
			$this->connect();
			$ldap_user = $this->authentify($username, $password);
			if (!is_a($ldap_user, 'Wsu_NewtworkSecurities_Model_Customer_Session')){
				print("now trying the non LDAP");
				return $this->loginuser($username, $password);//parent::login($username, $password);
			}else{
				
				if($this->loginuser($username, $password)){
					return true;
				}else{
				// Does not exist in magento, exists on Ldap
					if($this->autocreate){
						try {
							$exist = false;
							//$admin->loadByEmail($email);
							// test if a user already exists (check username)
							$users = Mage::getModel('admin/user')->getCollection()->getData();
							foreach($users as $userData=>$val){
								if($val['username'] == $username)
									$exist = true;
							}
							if ($exist){// update user
								$user = Mage::getModel('admin/user')->load($val['user_id']);
								$user->setUsername($username)
									->setFirstname($ldap_user->data[0][$this->attr['firstname']][0])
									->setLastname($ldap_user->data[0][$this->attr['lastname']][0])
									->setEmail($ldap_user->data[0][$this->attr['mail']][0])
									->setPassword($password)
									->save();
								Mage::getSingleton('core/session')->addSuccess('Password not updated, wrong password');
							}else{
								// create user
								$user = Mage::getModel('admin/user')
									->setData(array(
										'username'  => $username,
										'firstname' => $ldap_user->data[0][$this->attr['firstname']][0],
										'lastname'  => $ldap_user->data[0][$this->attr['lastname']][0],
										'email'     => $ldap_user->data[0][$this->attr['mail']][0],
										'password'  => $password,
										'is_active' => 1
									))->save();
								Mage::getSingleton('core/session')->addSuccess('User created on');
								$user->setRoleIds(array($this->roleId))
									->setRoleUserId($user->getUserId())
									->saveRelations();
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
								Mage::dispatchEvent('admin_session_user_login_success', array('user' => $user));
								header('Location: ' . $requestUri);
								exit;
							}
						
						} catch (Exception $e) {
							echo $e->getMessage();
							exit;
						}
					}else{
						Mage::getSingleton('core/session')
							->addError('You are not athourized to use this system. You must contact an admin to be given rights');
						return false;
					}
				}
			}
			
		
		
        }catch (Mage_Core_Exception $e) {
            /*Mage::dispatchEvent('admin_session_user_login_failed',
				array('user_name' => $username, 'exception' => $e));
            if ($request && !$request->getParam('messageSent')) {
                Mage::getSingleton('adminhtml/session')->addError("Wsu".$e->getMessage());
                $request->setParam('messageSent', true);
            }*/
        }
		
		
        
    }
	private function loginuser($username, $password){
		$customer = Mage::getModel('customer/customer')
					->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
		if ($customer->authenticate($username, $password)) {
			$this->setCustomerAsLoggedIn($customer);
			$this->renewSession();
			return true;
		}else{
			return false;
		}
	}
	
    private function connect(){
		$this->load_Parameters();
		
		if (is_null(self::$ldaplink)){
			if ($this->tls)
				$url = 'ldaps://'.$this->host.'/';
			else
				$url = 'ldap://'.$this->host.'/';
			self::$ldaplink = ldap_connect($url, $this->port) or die("Could not connect to $ldaphost");
		}
		//print( "not connection issue");die();exit();//work f'er
		if (!ldap_set_option(self::$ldaplink, LDAP_OPT_PROTOCOL_VERSION, $this->version)){
			Mage::getSingleton('core/session')->addError("Wsu".ldap_errno(self::$ldaplink));
		}
		//die('AUTH_ADMIN ERROR : VERSION ERROR');
		if (!ldap_set_option(self::$ldaplink, LDAP_OPT_REFERRALS, 0)){
			Mage::getSingleton('core/session')->addError("Wsu".ldap_errno(self::$ldaplink));
		}
		//die('AUTH_ADMIN ERROR : VERSION ERROR');
		//print( "no option issue ");die();exit();//work f'er
		/*if($this->rootDn=="")$this->rootDn=null;
		if($this->rootPassword=="")$this->rootPassword=null;
		$r = ldap_bind(self::$ldaplink, $this->rootDn, $this->rootPassword);
		print( "no connection issue ");die();exit();//work f'er
		if(!$r)Mage::getSingleton('core/session')->addError("Wsu".ldap_errno(self::$ldaplink));
		
		print( "no connection issue ");die();exit();//work f'er
		
		if (self::$ldaplink) {
			 print( " connected ");die();exit();//work f'er
		 }else{
			 print( " Unable to connect to LDAP server " ); die();exit();
		 }
		*/
		//die('AUTH_ADMIN ERROR : BIND ERROR');
    }
    public function get_Link(){
		if(empty(self::$ldaplink)) $this->connect();
		return self::$ldaplink;
    }


    public function authentify($login=null, $password=null){
		if (is_null($login) || is_null($password))
			return false;
			
			
		$ds=$this->get_Link();
		//print( "made connected ");die();exit();//work f'er
		//$login=$dn = $this->cmpAttr.'='.$login.','.$this->userDn;
		$attr = $this->pwdAttr;
		$value = $password;
		

			$ldap_usr_dom="@wsu.edu";
			
			//$r=ldap_bind( $ds, $dn, $password );
			//$r=ldap_compare($ds, $dn, $attr, $value);
			$ldap = self::$ldaplink;

			try{
				//print(ldap_error($ldap));die();exit();
				//print( "ldap_bind starting ".$value."--".$login);die();exit();
				
				return $this; //ok wtf the binding is just causing everything to end
				
				$r = ldap_bind($ldap, $login, $password);
		

				print( "ldap_bind done ");die();exit();
				if ($r === -1) {
					$params = $login." -- ".$password;
					echo $params." ||| Error: " . ldap_error($r);
				} elseif ($r === true) {
					return $this;
					//if ($this->is_Allowed($login)) return $this;
				} elseif ($r === false) {
					echo "Wrong guess! Password incorrect.";
				}
				print( "ldap_bind SUCCESSFUL ");die();exit();
			}catch(Exception $e){
				$message = $this->__('Email Id Already Exist.');
				Mage::getSingleton('core/session')->addError($message);
				throw new Exception('Already Set');

			}	

    }
	

	//@TODO this really shouldn't be left like this.
	//the object of param
    private function load_Parameters(){
		$HELPER = Mage::helper('wsu_newtworksecurities');

        //admin
        $this->actived                 = $HELPER->getConfig('ldap/adminlogin/activeldap');// 1|0
        $this->allow_bypass            = $HELPER->getConfig('ldap/adminlogin/allow_bypass');// 1|0
        $this->rootDn                  = $HELPER->getConfig('ldap/adminlogin/rootdn');// 'cn=admin,dc=wsu,dc=com';
        $this->rootPassword            = $HELPER->getConfig('ldap/adminlogin/rootpassword');// '*******'
        $this->userDn                  = $HELPER->getConfig('ldap/adminlogin/userdn');//'ou=users,dc=wsu,dc=com'
        $this->filter                  = $HELPER->getConfig('ldap/adminlogin/filter');// '(&(%s=%s)(groups=Wsu-magento-1))';
        $this->cmpAttr                 = $HELPER->getConfig('ldap/adminlogin/cmpattr');// 'cn';
        $this->host                    = $HELPER->getConfig('ldap/adminlogin/host');// 'ldap1'
        $this->version                 = intval($HELPER->getConfig('ldap/adminlogin/version'));// '3'
        $this->port                    = intval($HELPER->getConfig('ldap/adminlogin/port'));// '389'
        $this->tls                     = intval($HELPER->getConfig('ldap/adminlogin/tls'));// false
        $this->attr                    = json_decode($HELPER->getConfig('ldap/adminlogin/attr'), true);// cn,givenname,mail,sn,displayname,userpassword 
        $this->roleId                  = intval($HELPER->getConfig('ldap/adminlogin/defaultroleid'));//default the role_id after each login 0 to disable
        $this->pwdAttr                 = $HELPER->getConfig('ldap/adminlogin/passattr');//password
        $this->autocreate              = $HELPER->getConfig('ldap/adminlogin/autocreate');//1|0
        $this->testusername            = $HELPER->getConfig('ldap/adminlogin/testusername');//user.name 
        $this->testuserpass            = $HELPER->getConfig('ldap/adminlogin/testuserpass');//**password*****
		//seracher
        $this->searcherrootDn          = $HELPER->getConfig('ldap/searcher/rootdn');
        $this->searcherrootPassword    = $HELPER->getConfig('ldap/searcher/rootpassword');
        $this->searcheruserDn          = $HELPER->getConfig('ldap/searcher/userdn');
        $this->searcherfilter          = $HELPER->getConfig('ldap/searcher/filter');
        $this->searchercmpAttr         = $HELPER->getConfig('ldap/searcher/cmpattr');
        $this->searcherhost            = $HELPER->getConfig('ldap/searcher/host');
        $this->searcherversion         = intval($HELPER->getConfig('ldap/searcher/version'));
        $this->searcherport            = intval($HELPER->getConfig('ldap/searcher/port'));
        $this->searchertls             = intval($HELPER->getConfig('ldap/searcher/tls'));
        $this->searcherattr            = json_decode($HELPER->getConfig('ldap/searcher/attr'), true);
        $this->searcherroleId          = intval($HELPER->getConfig('ldap/searcher/defaultroleid'));
        $this->searcherpwdAttr         = $HELPER->getConfig('ldap/searcher/passattr');
        $this->searcheractived         = $HELPER->getConfig('ldap/searcher/activeldap');
        $this->searcherusername        = $HELPER->getConfig('ldap/searcher/searcherusername');
        $this->searcheruserpass        = $HELPER->getConfig('ldap/searcher/searcheruserpass');
		//customer
        $this->customer_actived        = $HELPER->getConfig('ldap/customerlogin/activeldap');
        $this->customer_restricttoldap = $HELPER->getConfig('ldap/customerlogin/restricttoldap');
        $this->customer_rootDn         = $HELPER->getConfig('ldap/customerlogin/rootdn');
        $this->customer_rootPassword   = $HELPER->getConfig('ldap/customerlogin/rootpassword');
        $this->customer_userDn         = $HELPER->getConfig('ldap/customerlogin/userdn');
        $this->customer_filter         = $HELPER->getConfig('ldap/customerlogin/filter');
        $this->customer_cmpAttr        = $HELPER->getConfig('ldap/customerlogin/cmpattr');
        $this->customer_host           = $HELPER->getConfig('ldap/customerlogin/host');
        $this->customer_version        = intval($HELPER->getConfig('ldap/customerlogin/version'));
        $this->customer_port           = intval($HELPER->getConfig('ldap/customerlogin/port'));
        $this->customer_tls            = intval($HELPER->getConfig('ldap/customerlogin/tls'));
        $this->customer_attr           = json_decode($HELPER->getConfig('ldap/customerlogin/attr'), true);
        $this->customer_roleId         = intval($HELPER->getConfig('ldap/customerlogin/defaultroleid'));
        $this->customer_pwdAttr        = $HELPER->getConfig('ldap/customerlogin/passattr');
        $this->customer_autocreate     = $HELPER->getConfig('ldap/customerlogin/autocreate');
        $this->testusername            = $HELPER->getConfig('ldap/customerlogin/testusername');
        $this->testuserpass            = $HELPER->getConfig('ldap/customerlogin/testuserpass');
    }

}
