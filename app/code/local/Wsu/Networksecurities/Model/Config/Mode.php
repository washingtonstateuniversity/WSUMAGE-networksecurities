<?php
/**
 * Networksecurities image model
 *
 * @category   Mage
 * @package    Wsu_Networksecurities
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Wsu_Networksecurities_Model_Config_Mode {
    /**
     * Get options for networksecurities mode selection field
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array(
                'label' => Mage::helper('wsu_networksecurities')->__('Always'),
                'value' => Wsu_Networksecurities_Helper_Data::MODE_ALWAYS
            ),
            array(
                'label' => Mage::helper('wsu_networksecurities')->__('After number of attempts to login'),
                'value' => Wsu_Networksecurities_Helper_Data::MODE_AFTER_FAIL
            ),
        );
    }
}
