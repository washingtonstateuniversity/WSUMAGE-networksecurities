<?php
/**
 * NewtworkSecurities image model
 *
 * @category   Mage
 * @package    Wsu_NewtworkSecurities
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Wsu_NewtworkSecurities_Model_Config_Mode {
    /**
     * Get options for newtworksecurities mode selection field
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array(
                'label' => Mage::helper('newtworksecurities')->__('Always'),
                'value' => Wsu_NewtworkSecurities_Helper_Data::MODE_ALWAYS
            ),
            array(
                'label' => Mage::helper('newtworksecurities')->__('After number of attempts to login'),
                'value' => Wsu_NewtworkSecurities_Helper_Data::MODE_AFTER_FAIL
            ),
        );
    }
}
