<?php

class Wsu_NewtworkSecurities_Block_Honeypot extends Mage_Core_Block_Template {
	protected function _construct(){
        parent::_construct();
    }
    /**
     * Renders newtworksecurities HTML (if required)
     *
     * @return string
    
    protected function _toHtml() {
        //$blockPath = Mage::helper('wsu_newtworksecurities')->getNewtworkSecurities($this->getFormId())->getBlockName();
		//$block = $this->getLayout()->createBlock($blockPath);
        //$block->setData($this->getData());

		$block = Mage::app()->getLayout()->createBlock('cms/block')->setTemplate('wsu/newtworksecurities/honeypot.phtml'); 

		
        
        return $block->toHtml();
    }
	
    protected $_template = 'wsu/newtworksecurities/honeypot.phtml';
 */


    public function getHoneypotName(){
        /* @var $helper Wsu_newtworksecurities_Helper_Data */
        $helper = Mage::helper('wsu_newtworksecurities');
        return $helper->getHoneypotName();
    }
	
	
}
