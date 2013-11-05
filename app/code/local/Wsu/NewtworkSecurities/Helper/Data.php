<?php
class Wsu_NewtworkSecurities_Helper_Data extends Mage_Core_Helper_Abstract {
	
    public function getConfig($path,$store = null,$default = null) {
        $value = trim(Mage::getStoreConfig("wsu_newtworksecurities/$path", $store));
        return (!isset($value) || $value == '')? $default : $value ;
    }
    public function getHoneypotName(){
        return Mage::getStoreConfig('wsu_newtworksecurities/honeypot/honeypotName');
    }	
    public function log($data) {
        if (is_array($data) || is_object($data)) {
            $data = print_r($data, true);
        }
        Mage::log($data, null, 'wsu-newtworksecurities.log');
    }
	/*
		//must have a want to report bad block
		public function httpbl_blockme() {
			header('HTTP/1.0 403 Forbidden');
			echo '<html><body>';
			//$this->httpbl_infected(); // inform the user that he might be infected
			// write the javascript needed to let the user in and later log it.
			$js = '<script type="text/javascript">
					function setcookie( name, value, expires, path, domain, secure ) {
						
						// set time, in milliseconds
						var today = new Date();
						today.setTime( today.getTime() );
						if ( expires ) {
							expires = expires * 1000 * 60 * 60 * 24;
						}
						var expires_date = new Date( today.getTime() + (expires) );
			
						document.cookie = name + "=" +escape( value ) +
						( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) + 
						( ( path ) ? ";path=" + path : "" ) + 
						( ( domain ) ? ";domain=" + domain : "" ) +
						( ( secure ) ? ";secure" : "" );
					}    
					function letmein() {
						setcookie("notabot","true",1,"/", "", "");
						location.reload(true);
					}
					</script>
				<br />';
			//output the body
			echo $js . JText::_('HTTPBL_LET_ME_IN');
		}
		// Add a line to the log table
		public function httpbl_add_log($ip, $user_agent, $response, $blocked) {
			global $GLOBALS;
			$time    = gmdate("Y-m-d H:i:s", time() + get_option('gmt_offset') * 60 * 60);
			$blocked = ($blocked ? 1 : 0);
			$user_agent = mysql_real_escape_string($user_agent);
		}
	*/
	
	/**
     * Show newtworksecurities only after certain number of unsuccessful attempts
     */
    const MODE_AFTER_FAIL = 'after_fail';

    /**
     * List uses Models of NewtworkSecurities
     * @var array
     */
    protected $_newtworksecurities = array();

    /**
     * Get NewtworkSecurities
     *
     * @param string $formId
     * @return Wsu_NewtworkSecurities_Model_Interface

    public function getNewtworkSecurities($formId){
        if (!array_key_exists($formId, $this->_newtworksecurities)) {
            $type = $this->getConfigNode('type');
            $this->_newtworksecurities[$formId] = Mage::getModel('newtworksecurities/' . $type, array('formId' => $formId));
        }
        return $this->_newtworksecurities[$formId];
    }     */
}
