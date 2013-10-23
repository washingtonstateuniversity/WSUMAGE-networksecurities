<?php
class Wsu_Admin_Block_System_Config_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {
    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $html = 'Ldap';// note this is going to be switch from the help approch.  Look to the the launcher module
        return $html;
    }
}
