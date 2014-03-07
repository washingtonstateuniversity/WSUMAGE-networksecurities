<?php
class Wsu_NetworkSecurities_Model_Resource_Blacklist extends Mage_Core_Model_Resource_Db_Abstract {
    public function _construct() {    
        $this->_init('wsu_networksecurities/blacklist', 'blacklist_id');
    }
}
