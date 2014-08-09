<?php
class Wsu_Networksecurities_Block_Adminhtml_Blacklist extends Mage_Adminhtml_Block_Widget_Grid_Container {
  public function __construct(){
    $this->_controller = 'adminhtml_Blacklist';
    $this->_blockGroup = 'Blacklist';
    $this->_headerText = Mage::helper('wsu_networksecurities')->__('Blacklist');
    parent::__construct();
    $this->_removeButton('add');
  }
}