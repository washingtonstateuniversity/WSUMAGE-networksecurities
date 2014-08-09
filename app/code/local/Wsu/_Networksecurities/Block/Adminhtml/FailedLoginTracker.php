<?php
class Wsu_Networksecurities_Block_Adminhtml_FailedLoginTracker extends Mage_Adminhtml_Block_Widget_Grid_Container {
  public function __construct(){
    $this->_controller = 'adminhtml_failedLoginTracker';
    $this->_blockGroup = 'failedlogintracker';
    $this->_headerText = Mage::helper('wsu_networksecurities')->__('Failed Login List');
    parent::__construct();
    $this->_removeButton('add');
  }
}