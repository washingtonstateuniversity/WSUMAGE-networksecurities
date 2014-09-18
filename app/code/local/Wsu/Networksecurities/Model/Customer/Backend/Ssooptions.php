<?php
class Wsu_Networksecurities_Model_Customer_Backend_Ssooptions extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract {

	public function beforeSave($object) {
		$attributeCode = $this->getAttribute()->getName();
		if ($attributeCode == 'my_attribute') {
			$data = $object->getData($attributeCode);
			if (!is_array($data)) {
				$data = array();
			}
			$object->setData($attributeCode, join(',', $data));
		}
		return $this;
	}
	public function afterLoad($object) {
		$attributeCode = $this->getAttribute()->getName();
		if ($attributeCode == 'my_attribute') {
			$data = $object->getData($attributeCode);
			if ($data) {
				$object->setData($attributeCode, split(',', $data));
			}
		}
		return $this;
	} 
}
