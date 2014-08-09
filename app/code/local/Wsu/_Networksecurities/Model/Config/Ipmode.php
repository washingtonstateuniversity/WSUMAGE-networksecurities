<?php
class Wsu_Networksecurities_Model_Config_IPMode {
    /**
     * Get options for networksecurities mode selection field
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array(
                'label' => Mage::helper('wsu_networksecurities')->__('Exclude'),
                'value' => Wsu_Networksecurities_Helper_Data::IPMODE_EXCLUDE
            ),
            array(
                'label' => Mage::helper('wsu_networksecurities')->__('Include'),
                'value' => Wsu_Networksecurities_Helper_Data::IPMODE_INCLUDE
            ),
        );
    }
}
