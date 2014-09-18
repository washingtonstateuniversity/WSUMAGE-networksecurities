<?php
class Wsu_Networksecurities_Block_Sso_Toplinks extends Mage_Core_Block_Template {
	public function __construct() {
		parent::__construct();
		$this->setTemplate('wsu/networksecurities/sso/providers_block.phtml');
	}


	public function isShowButton($provider) {
		return (int) Mage::getStoreConfig("wsu_networksecurities/${provider}_login/is_active",Mage::app()->getStore()->getId());
	}

	public function getIsActive() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/general_sso/is_active',Mage::app()->getStore()->getId());
	}	

	public function getButton($provider) {
		$html = "";
		$html = $this->getLayout()->createBlock('wsu_networksecurities/sso_providers')
					->setData('provider', $provider)
					->setTemplate('wsu/networksecurities/sso/bt.phtml')->toHtml();
					
		if( $this->isShowButton($provider) ){
			$out=array(
					'button'=> $html,
					'check' =>$this->isShowButton($provider),
					'id'	=> 'bt-loginfb',
					'sort'  => $this->sortOrder($provider)
					);
		}else{
			$out=array();
		}
		return $out;
	}

    protected function _beforeToHtml() {
		if(!$this->getIsActive()) {
			$this->setTemplate(null);
		}
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->setTemplate(null);
		}
		$this->getTemplate();
		if(Mage::registry('shown_sociallogin_button')) {
			$this->setTemplate(null);
		}elseif($this->getTemplate()) {
			Mage::register('shown_sociallogin_button',true);
		}
		
		return parent::_beforeToHtml();
	}	

	public function sortOrder($provider) {
		return (int) Mage::getStoreConfig("wsu_networksecurities/${provider}_login/sort_order",Mage::app()->getStore()->getId());
	}
	
	public function makeArrayButton() {
		$buttonArray = array();
		$providers=Mage::getModel('wsu_networksecurities/customer_source_ssooptions')->getAllOptions();
		foreach($providers as $provider){
			if ($this->isShowButton($provider['value'])){
				$buttonArray[] = $this->getButton($provider['value']);
			}
		}

		usort($buttonArray, array($this, 'compareSortOrder'));
		return $buttonArray;
	}
	
	public function compareSortOrder($a, $b) {
		if ($a['sort'] == $b['sort']) return 0;
		return $a['sort'] < $b['sort'] ? -1 : 1;
	}
	
	public function getNumberShow() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/general_sso/number_show',Mage::app()->getStore()->getId());
	}
}