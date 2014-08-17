<?php
class Wsu_Networksecurities_Model_Config_Share extends Mage_Customer_Model_Config_Share {
    /**
     * Check for username duplicates before saving customers sharing options
     *
     * @return Mage_Customer_Model_Config_Share
     * @throws Mage_Core_Exception
     */
    public function _beforeSave() {
        parent::_beforeSave();

        $value = $this->getValue();
        if ($value == self::SHARE_GLOBAL) {
            if (Mage::getResourceSingleton('customer/customer')->findUsernameDuplicates()) {
                Mage::throwException(
                    Mage::helper('wsu_networksecurities')->__('Cannot share customer accounts globally because some customer accounts with the same username exist on multiple websites and cannot be merged.')
                );
            }
        }
        return $this;
    }
}
