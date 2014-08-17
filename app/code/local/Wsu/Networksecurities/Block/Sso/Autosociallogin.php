<?php
class Wsu_Networksecurities_Block_Sso_Autosociallogin extends Wsu_Networksecurities_Block_Sso_Sociallogin {	
	public function getShownPositions() {
		$shownpositions = Mage::getStoreConfig('wsu_networksecurities/general_sso/position',Mage::app()->getStore()->getId());
		$shownpositions = explode(',',$shownpositions);
		//Zend_debug::dump($this->getBlockPosition());
		//Zend_debug::dump($shownpositions);die();
		return $shownpositions;
	}
	
	public function isShow() {	
		if(in_array($this->getBlockPosition(),$this->getShownPositions())) {
			return true;
		}
		return false;
	}
	
	protected function _beforeToHtml() {
		if(!$this->isShow()) {
			$this->setTemplate(null);
		}		
		return parent::_beforeToHtml();
	}
}