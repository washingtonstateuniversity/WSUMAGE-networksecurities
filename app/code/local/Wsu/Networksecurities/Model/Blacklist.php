<?php
class Wsu_Networksecurities_Model_Blacklist extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('wsu_networksecurities/blacklist');
    }
}
