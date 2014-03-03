<?php
class Wsu_NetworkSecurities_Model_Captcha_Api extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('wsu_networksecurities/captcha_api');
	}
	public function toOptionArray() {
		$helper = Mage::helper('wsu_networksecurities');
		$options = array(
			array(
				'value' => 'standard',
				'label' => $helper->__('Standard')
			),
			array(
				'value' => 'ajax',
				'label' => $helper->__('Ajax')
			)
		);
		return $options;
	}
}