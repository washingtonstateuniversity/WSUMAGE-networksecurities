<?php
class Wsu_Networksecurities_Model_Entity_Customer extends Mage_Customer_Model_Resource_Customer {
    
    protected function _beforeSave(Varien_Object $customer) {
        parent::_beforeSave($customer);

        if (Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled')) {
            if ($customer->getSharingConfig()->isWebsiteScope()) {
                $websiteId = (int) $customer->getWebsiteId();
            }else{
                $websiteId = null;
            }

            $model = Mage::getModel('customer/customer');
            $result = $model->customerUsernameExists($customer->getUsername(), $websiteId);
            if ($result && $result->getId() != $customer->getId()) {
                throw Mage::exception('Mage_Core', Mage::helper('wsu_networksecurities')->__("Username already exists"));
            }
        }

        return $this;
    }
    
    protected function _getDefaultAttributes() {
        $attributes = parent::_getDefaultAttributes();
        array_push($attributes, 'is_active');
        return $attributes;
    }
    
    /**
     * Load customer by username
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param string $username
     * @return Mage_Customer_Model_Entity_Customer
     * @throws Mage_Core_Exception
     */
    public function loadByUsername(Mage_Customer_Model_Customer $customer, $username) {
        if (!Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/case_sensitive')) {
            $filter = new Zend_Filter_StringToLower(array('encoding' => 'UTF-8'));
            $username = $filter->filter($username);
        }
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->joinNatural(array('cev' => $this->getTable('customer_entity_varchar')))
            ->joinNatural(array('ea' => $this->getTable('eav/attribute')))
            ->where('ea.attribute_code=\'username\' AND cev.value=?',$username);

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                Mage::throwException(Mage::helper('customer')->__('Customer website ID must be specified when using the website scope.'));
            }
            $select->where('website_id=?', (int)$customer->getWebsiteId());
        }

        if ($id = $this->_getReadAdapter()->fetchOne($select, 'entity_id')) {
            $this->load($customer, $id);
        }
        else {
            $customer->setData(array());
        }
        return $this;
    }
	
	
    public function loadBySso(Mage_Customer_Model_Customer $customer, $data) {
		$provider=$data['provider'];
		$uid_name=$data['username'];
		$uid_email=$data['email'];

		$map_name=$provider.'":"'.$uid_name;
		$map_email=$provider.'":"'.$uid_email;


        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->joinNatural(array('cev' => $this->getTable('customer_entity_varchar')))
            ->joinNatural(array('ea' => $this->getTable('eav/attribute')))
            ->where('ea.attribute_code=\'sso_map\' AND (cev.value LIKE CONCAT(\'%\',?,\'%\') OR cev.value LIKE CONCAT(\'%\',?,\'%\'))',$map_name,$map_email);

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                Mage::throwException(Mage::helper('customer')->__('Customer website ID must be specified when using the website scope.'));
            }
            $select->where('website_id=?', (int)$customer->getWebsiteId());
        }

        if ($id = $this->_getReadAdapter()->fetchOne($select, 'entity_id')) {
            $this->load($customer, $id);
        }
        else {
            $customer->setData(array());
        }
        return $this;
    }
    /**
     * Check whether there are username duplicates of customers in global scope
     *
     * @return bool
     */
    public function findUsernameDuplicates() {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(array('cev' => $this->getTable('customer_entity_varchar')), array('cnt' => 'COUNT(*)'))
            ->joinLeft(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = cev.attribute_id')
            ->where('ea.attribute_code=\'username\'')
            ->group('cev.value')
            ->order('cnt DESC')
            ->limit(1);

        $lookup = $adapter->fetchRow($select);
        if (empty($lookup)) {
            return false;
        }
        return $lookup['cnt'] > 1;
    }
	
	public function addSsoMap($customer,$data) {
		$map = $customer->getSsoMap();
		if(isset($map)){
			$map = (array)json_decode($map);
		}
		$provider=$data['provider'];
		if(isset($provider)){
			if(!isset($map[$provider])){
				$map[$provider]=$data['email'];
				if(Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled') && isset($data['username'])){
					$map[$provider]=$data['username'];
				}
			}
		}
		$customer->setSsoMap( json_encode($map) );
		$customer->save();
		return $customer;
	}
	
	
	
	
}