<?php
/**
 * Custom renderer for WSU Admin LDAP test popup
 */
class Wsu_Networksecurities_Block_Adminhtml_System_Config_ApiWizard extends Mage_Adminhtml_Block_System_Config_Form_Field {
    /**
     * Get the session model to test on
     */
    public function get_session_Model() {
        return Mage::getModel('admin/session');
    }
    /**
     * Get the session model to test on
     */
    public function test_config() {
        $model_obj = $this->get_session_Model();
        $username  = trim(Mage::getStoreConfig('wsu_networksecurities/ldap/adminlogin/testusername'));
        $password  = trim(Mage::getStoreConfig('wsu_networksecurities/ldap/adminlogin/testuserpass'));
        $result    = $model_obj->authentify($username, $password);
        if (!$result) {
            echo "failed";
            return false;
        }
        return true;
    }
    /**
     * Set template to itself
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('wsu/networksecurities/system/config/api_wizard.phtml');
        }
        return $this;
    }
    /**
     * Unset some non-related element parameters
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$request = Mage::app()->getRequest();
		$testing=$request->getParam('testing');
        if (isset($testing)) {
            if ($this->test_config())
                echo "pass";
        }
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => Mage::helper('admin')->__($originalData['button_label']),
            'button_url' => $this->getUrl('*/system_config/edit/section/wsu_newtworksecuritie'), //$originalData['button_url'],
            'html_id' => $element->getHtmlId()
        ));
        return $this->_toHtml();
    }
}

