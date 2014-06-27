<?php
class Wsu_NetworkSecurities_Block_Adminhtml_Permissions_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main {

  protected function _prepareForm() {
	  
        parent::_prepareForm();        
        $form = $this->getForm();
        $fieldset = $form->getElements()->searchById('base_fieldset');
        
	  
        $fieldset->addField('ldap_user', 'select', array(
            'name'      => 'is_active',
            'label'     => Mage::helper('adminhtml')->__('LDAP user'),
            'id'        => 'is_active',
            'title'     => Mage::helper('adminhtml')->__('LDAP user'),
            'class'     => 'input-select',
            'style'     => 'width: 80px',
            'options'   => array('1' => Mage::helper('adminhtml')->__('Yes'), '0' => Mage::helper('adminhtml')->__('No')),
        )); 
		$this->setForm($form);
		
		return $this;
		
  }

}
