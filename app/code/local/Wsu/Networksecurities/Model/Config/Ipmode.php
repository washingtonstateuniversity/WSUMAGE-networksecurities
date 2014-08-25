<?php
class Wsu_Networksecurities_Model_Config_Ipmode {
    /**
     * Get options for networksecurities mode selection field
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array(
                'label' => Mage::helper('wsu_networksecurities')->__('Exclude'),
                'value' => 'exclude'
            ),
            array(
                'label' => Mage::helper('wsu_networksecurities')->__('Include'),
                'value' => 'include'
            ),
        );
    }
}
