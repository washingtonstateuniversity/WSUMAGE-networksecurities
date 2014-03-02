<?php
class Wsu_NetworkSecurities_Model_Captcha_Language extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('wsu_networksecurities/captcha_language');
	}
	public function toOptionArray() {
		//note there should be a better lang handling here
		$helper = Mage::helper('wsu_networksecurities');
		return array(
			array(
				'value' => 'en',
				'label' => $helper->__('English')
			),
			array(
				'value' => 'nl',
				'label' => $helper->__('Dutch')
			),
			array(
				'value' => 'fr',
				'label' => $helper->__('French')
			),
			array(
				'value' => 'de',
				'label' => $helper->__('German')
			),
			array(
				'value' => 'pt',
				'label' => $helper->__('Portuguese')
			),
			array(
				'value' => 'ru',
				'label' => $helper->__('Russian')
			),
			array(
				'value' => 'es',
				'label' => $helper->__('Spanish')
			),
			array(
				'value' => 'tr',
				'label' => $helper->__('Turkish')
			)
		);
	}
}