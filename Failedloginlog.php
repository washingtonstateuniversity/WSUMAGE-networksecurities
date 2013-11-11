<?php
/**
 * @package    Caracri_FailedLoginTracker
 * @author     Caracri Works
 * @copyright  Copyright (c) 2011 Caracri Works (http://www.caracri-works.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Caracri_FailedLoginTracker_Model_Failedloginlog extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('failedlogintracker/failedloginlog');
    }
    
	public function _beforeSave()
	{
        parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if (!$this->getCreatedAt()) {
        	$this->setCreatedAt($now);
		}
        
        return $this;
	}
	
	public function deleteExpiredLog()
	{
		$now = Mage::getModel('core/date')->timestamp(time());
		$now = new Zend_Date($now);
		$now->subDay(7);
		
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tabel = Mage::getSingleton('core/resource')->getTableName('failedlogin_log');
		$sql = "DELETE FROM {$tabel} WHERE created_at < ?";
		$db->query($sql, array($now->toString('yyyy-MM-dd')));
	}
}
