<?php
class Wsu_NetworkSecurities_Model_Config_IPMode {
    /**
     * Get options for networksecurities mode selection field
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array(
                'label' => Mage::helper('wsu_networksecurities')->__('Exclude'),
                'value' => Wsu_NetworkSecurities_Helper_Data::IPMODE_EXCLUDE
            ),
            array(
                'label' => Mage::helper('wsu_networksecurities')->__('Include'),
                'value' => Wsu_NetworkSecurities_Helper_Data::IPMODE_INCLUDE
            ),
        );
    }
}
