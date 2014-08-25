<?php
class Wsu_Networksecurities_Model_Config_Logging {
    /**
     * Get options for networksecurities mode selection field
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array(
                'label' => Mage::helper('wsu_networksecurities')->__("No logging"),
                'value' => 'none'
            ),
            array(
                'label' => Mage::helper('wsu_networksecurities')->__("Major Issues only"),
                'value' => 'light'
            ),
			array(
                'label' => Mage::helper('wsu_networksecurities')->__("Full"),
                'value' => 'full'
            ),
        );
    }
}
