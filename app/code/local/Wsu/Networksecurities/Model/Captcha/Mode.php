<?php
class Wsu_Networksecurities_Model_Captcha_Mode extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('wsu_networksecurities/captcha_mode');
	}
	public function toOptionArray($default = false) {
		$helper = Mage::helper('wsu_networksecurities');
		$options = array(
			array(
				'value' => 'auto',
				'label' => $helper->__('Auto (hidden for logged in customers)')
			),
			array(
				'value' => 'always',
				'label' => $helper->__('Always on')
			),
			array(
				'value' => 'off',
				'label' => $helper->__('Off')
			)
		);
		if ($default) {
			$options = array_merge(array(
				array(
					'value' => 'default',
					'label' => $helper->__('Default')
				)
			), $options);
		}
		return $options;
	}
}