<?php
/**
 * NetworkSecurities image model
 *
 * @category   Mage
 * @package    Wsu_NetworkSecurities
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Wsu_NetworkSecurities_Model_Config_Mode {
    /**
     * Get options for networksecurities mode selection field
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array(
                'label' => Mage::helper('networksecurities')->__('Always'),
                'value' => Wsu_NetworkSecurities_Helper_Data::MODE_ALWAYS
            ),
            array(
                'label' => Mage::helper('networksecurities')->__('After number of attempts to login'),
                'value' => Wsu_NetworkSecurities_Helper_Data::MODE_AFTER_FAIL
            ),
        );
    }
}
