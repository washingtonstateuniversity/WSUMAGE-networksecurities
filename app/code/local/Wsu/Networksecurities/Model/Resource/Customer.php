<?php
class Wsu_Networksecurities_Model_Resource_Customer extends Mage_Core_Model_Resource_Db_Abstract {
    public function _construct() {    
        // Note that the membership_id refers to the key field in your database table.
        $this->_init('wsu_networksecurities/customer', 'twitter_customer_id');
    }
}