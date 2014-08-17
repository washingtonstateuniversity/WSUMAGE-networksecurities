<?php
class Wsu_Networksecurities_Model_Spamlog extends Mage_Core_Model_Abstract {
    /**
     * Type Remote Address
     */
    const TYPE_REMOTE_ADDRESS = 1;

    /**
     * Type User Login Name
     */
    const TYPE_LOGIN = 2;

    public function _construct() {
        parent::_construct();
        $this->_init('wsu_networksecurities/spam_log');
    }
    
	public function _beforeSave() {
        parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if (!$this->getCreatedAt()) {
        	$this->setCreatedAt($now);
		}
        return $this;
	}


    /**
     * Save or Update count Attempts
     *
     * @param string|null $login
     * @return Wsu_Networksecurities_Model_Resource_Log
     */
    public function logAttempt($login) {
        if (!is_null($login)) {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                array(
                     'type' => self::TYPE_LOGIN, 'value' => $login, 'count' => 1,
                     'updated_at' => Mage::getSingleton('core/date')->gmtDate()
                ),
                array('count' => new Zend_Db_Expr('count+1'), 'updated_at')
            );
        }
        $ip = Mage::helper('core/http')->getRemoteAddr();
        if (!is_null($ip)) {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                array(
                     'type' => self::TYPE_REMOTE_ADDRESS, 'value' => $ip, 'count' => 1,
                     'updated_at' => Mage::getSingleton('core/date')->gmtDate()
                ),
                array('count' => new Zend_Db_Expr('count+1'), 'updated_at')
            );
        }
        return $this;
    }

    /**
     * Delete User attempts by login
     *
     * @param string $login
     * @return Wsu_Networksecurities_Model_Resource_Log
     */
    public function deleteUserAttempts($login) {
        if (!is_null($login)) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                array('type = ?' => self::TYPE_LOGIN, 'value = ?' => $login)
            );
        }
        $ip = Mage::helper('core/http')->getRemoteAddr();
        if (!is_null($ip)) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(), array('type = ?' => self::TYPE_REMOTE_ADDRESS, 'value = ?' => $ip)
            );
        }

        return $this;
    }

    /**
     * Get count attempts by ip
     *
     * @return null|int
     */
    public function countAttemptsByRemoteAddress() {
        $ip = Mage::helper('core/http')->getRemoteAddr();
        if (!$ip) {
            return 0;
        }
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable(), 'count')->where('type = ?', self::TYPE_REMOTE_ADDRESS)
            ->where('value = ?', $ip);
        return $read->fetchOne($select);
    }

    /**
     * Get count attempts by user login
     *
     * @param string $login
     * @return null|int
     */
    public function countAttemptsByUserLogin($login) {
        if (!$login) {
            return 0;
        }
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable(), 'count')->where('type = ?', self::TYPE_LOGIN)
            ->where('value = ?', $login);
        return $read->fetchOne($select);
    }

    /**
     * Delete attempts with expired in update_at time
     *
     * @return void
     */
    public function deleteOldAttempts() {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array('updated_at < ?' => Mage::getSingleton('core/date')->gmtDate(null, time() - 60*30))
        );
    }	
	public function deleteExpiredLog() {
		$now = Mage::getModel('core/date')->timestamp(time());
		$now = new Zend_Date($now);
		$now->subDay(7);
		
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tabel = Mage::getSingleton('core/resource')->getTableName('failedlogin_log');
		$sql = "DELETE FROM {$tabel} WHERE created_at < ?";
		$db->query($sql, array($now->toString('yyyy-MM-dd')));
	}
}
