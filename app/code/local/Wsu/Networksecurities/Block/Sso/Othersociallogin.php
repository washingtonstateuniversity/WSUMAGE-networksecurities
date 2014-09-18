<?php
class Wsu_Networksecurities_Block_Sso_Othersociallogin extends Mage_Core_Block_Template { 
 	var $_cID = null;
	var $_customerData = null;
	public function __construct() {
		parent::__construct();
		
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			 $this->_customerData = Mage::getSingleton('customer/session')->getCustomer();
			 $this->_cID = $this->_customerData->getId();
		 }
		
		
		$this->setData('isOther', true)->setTemplate('wsu/networksecurities/sso/providers_block.phtml');
	}


	public function isShowButton($provider) {
		return (int) Mage::getStoreConfig("wsu_networksecurities/${provider}_login/is_active",Mage::app()->getStore()->getId());
	}

	public function getIsActive() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/general_sso/is_active',Mage::app()->getStore()->getId());
	}	

	public function getButton($provider,$skipUrls=false) {//die('getButton');
		$html = "";
		$html = $this->getLayout()->createBlock('wsu_networksecurities/sso_providers')
					->setData('provider', $provider)
					->setData('account', $this->_cID)
					->setData('skipUrl', $skipUrls)
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

    protected function _beforeToHtml() {//die('_beforeToHtml');
		if(!$this->getIsActive()) {
			$this->setTemplate(null);
		}
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			//$this->setTemplate(null);
		}
		$this->getTemplate();
		if(Mage::registry('shown_sociallogin_button')) {
			//$this->setTemplate(null);
		}elseif($this->getTemplate()) {
			//Mage::register('shown_sociallogin_button',true);
		}
		
		return parent::_beforeToHtml();
	}	

	public function sortOrder($provider) {
		return (int) Mage::getStoreConfig("wsu_networksecurities/${provider}_login/sort_order",Mage::app()->getStore()->getId());
	}
	

	public function getUsedSsoBtns() {
		$buttonArray = array();
		
		$_excludeSso=array();
		
		$_ssoMap=$this->_customerData->getSsoMap();
		if(isset($_ssoMap)){
			$_excludeSso = (array)json_decode($_ssoMap);	
		}
		$providers=Mage::getModel('wsu_networksecurities/customer_source_ssooptions')->getAllOptions();
		foreach($providers as $provider){
			$providerKey = $provider['value'];
			if ($this->isShowButton($providerKey) && isset($_excludeSso[$providerKey])){
				$buttonArray[] = $this->getButton($providerKey,true);
			}
		}

		usort($buttonArray, array($this, 'compareSortOrder'));
		return $buttonArray;
	}
	public function getSsoBtns($skipUrls=false) {
		$buttonArray = array();
		
		$_excludeSso=array();
		
		$_ssoMap=$this->_customerData->getSsoMap();
		if(isset($_ssoMap)){
			$_excludeSso = (array)json_decode($_ssoMap);	
		}
		$providers=Mage::getModel('wsu_networksecurities/customer_source_ssooptions')->getAllOptions();
		foreach($providers as $provider){
			$providerKey = $provider['value'];
			if ($this->isShowButton($providerKey) && !isset($_excludeSso[$providerKey])){
				$buttonArray[] = $this->getButton($providerKey,$skipUrls);
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