<?php
class Wsu_Networksecurities_Model_Resource_Blacklist_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('wsu_networksecurities/blacklist');
    }
}
