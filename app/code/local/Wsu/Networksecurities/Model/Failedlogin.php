<?php
class Wsu_Networksecurities_Model_Failedlogin extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('wsu_networksecurities/failedlogin');
    }
/*    
	public function _beforeSave() {
        parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if (!$this->getCreatedAt()) {
        	$this->setCreatedAt($now);
		}
        return $this;
	}
	*/
	
	
}
