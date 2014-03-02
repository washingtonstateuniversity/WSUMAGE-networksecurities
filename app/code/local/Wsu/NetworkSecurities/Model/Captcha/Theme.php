<?php
class Wsu_NetworkSecurities_Model_Captcha_Theme extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('wsu_networksecurities/captcha_theme');
	}
	public function toOptionArray() {
		$helper = Mage::helper('wsu_networksecurities');
		return array(
			array(
				'value' => 'red',
				'label' => $helper->__('Red')
			),
			array(
				'value' => 'white',
				'label' => $helper->__('White')
			),
			array(
				'value' => 'blackglass',
				'label' => $helper->__('Blackglass')
			),
			array(
				'value' => 'clean',
				'label' => $helper->__('Clean')
			)
		);
	}
}