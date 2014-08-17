<?php
class Wsu_Networksecurities_Model_Config_Source_InputValidation {
    public function toOptionArray() {
        $helper = Mage::helper('wsu_networksecurities');
        return array(
            array('value'=>'default', 'label'=> $helper->__('Default (letters, digits and _- characters)')),
            array('value'=>'alphanumeric', 'label'=> $helper->__('Letters and digits')),
            array('value'=>'alpha', 'label'=> $helper->__('Letters only')),
            array('value'=>'numeric', 'label'=> $helper->__('Digits only')),
            array('value'=>'custom', 'label'=> $helper->__('Custom (PCRE Regex)')),
        );
    }
}
