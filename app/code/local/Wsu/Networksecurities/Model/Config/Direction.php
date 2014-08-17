<?php
class Wsu_Networksecurities_Model_Config_Direction {
    public function toOptionArray() {
        return array(
            array('value' => 'left', 'label'=>Mage::helper('adminhtml')->__('Left to Right')),
            array('value' => 'right', 'label'=>Mage::helper('adminhtml')->__('Right to Left')),
		);
    }
}