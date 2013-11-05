<?php
/**
 * Data source to fill "Forms" field
 *
 * @category   Mage
 * @package    Wsu_NewtworkSecurities
 */
abstract class Wsu_NewtworkSecurities_Model_Config_Form_Abstract extends Mage_Core_Model_Config_Data{
    /**
     * @var string
     */
    protected $_configPath;

    /**
     * Returns options for form multiselect
     *
     * @return array
     */
    public function toOptionArray() {
        $optionArray = array();
        /* @var $backendNode Mage_Core_Model_Config_Element */
        $backendNode = Mage::getConfig()->getNode($this->_configPath);
        if ($backendNode) {
            foreach ($backendNode->children() as $formNode) {
                /* @var $formNode Mage_Core_Model_Config_Element */
                if (!empty($formNode->label)) {
                    $optionArray[] = array('label' => (string)$formNode->label, 'value' => $formNode->getName());
                }
            }
        }
        return $optionArray;
    }
}
