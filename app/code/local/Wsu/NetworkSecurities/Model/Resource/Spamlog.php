<?php
class Wsu_NetworkSecurities_Model_Resource_Spamlog extends Mage_Core_Model_Resource_Db_Abstract {
    public function _construct() {    
        $this->_init('wsu_networksecurities/spam_log', 'spamlog_id');
    }
}
