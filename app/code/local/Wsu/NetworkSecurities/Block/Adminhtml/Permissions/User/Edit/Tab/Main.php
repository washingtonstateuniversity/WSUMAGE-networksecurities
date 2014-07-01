<?php
class Wsu_NetworkSecurities_Block_Adminhtml_Permissions_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Main {

  protected function _prepareForm() {

		parent::_prepareForm();   
		
		$HELPER = Mage::helper('wsu_networksecurities');

        if($HELPER->getConfig('adminlogin/activeldap')){
			$form = $this->getForm();
	
			$fieldset = $form->addFieldset('LDAP', array(
				'legend' => Mage::helper('adminhtml')->__('Active Directory'),
				'class' => 'fieldset-wide'
			));
	
			$fieldset->addField('ldap_user', 'select', array(
				'name'      => 'is_active',
				'label'     => Mage::helper('adminhtml')->__('LDAP user'),
				'id'        => 'is_active',
				'title'     => Mage::helper('adminhtml')->__('LDAP user'),
				'class'     => 'input-select',
				'disabled'	=> true,
				'style'     => 'width: 80px',
				'options'   => array('1' => Mage::helper('adminhtml')->__('Yes'), '0' => Mage::helper('adminhtml')->__('No')),
				'after_element_html' => '<br/><small>'.Mage::helper('adminhtml')->__('This field is automaticly detected if you log in with an AD account.  If you change the user name and it doesn\'t match an AD user, then this will move to no.').'</small>',
			)); 
		}
		return $this;
		
  }

}
