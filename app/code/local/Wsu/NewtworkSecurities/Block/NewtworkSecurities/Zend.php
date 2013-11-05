<?php
class Wsu_NewtworkSecurities_Block_NewtworkSecurities_Zend extends Mage_Core_Block_Template {
    protected $_template = 'newtworksecurities/zend.phtml';
    /**
     * @var string
     */
    protected $_newtworksecurities;
    /**
     * Returns template path
     *
     * @return string
     */
    public function getTemplate() {
        return $this->getIsAjax() ? '' : $this->_template;
    }
    /**
     * Returns URL to controller action which returns new newtworksecurities image
     *
     * @return string
     */
    public function getRefreshUrl() {
        return Mage::getUrl(Mage::app()->getStore()->isAdmin() ? 'adminhtml/refresh/refresh' : 'newtworksecurities/refresh', array(
            '_secure' => Mage::app()->getStore()->isCurrentlySecure()
        ));
    }
    /**
     * Renders newtworksecurities HTML (if required)
     *
     * @return string
     */
    protected function _toHtml() {
        if ($this->getNewtworkSecuritiesModel()->isRequired()) {
            $this->getNewtworkSecuritiesModel()->generate();
            return parent::_toHtml();
        }
        return '';
    }
    /**
     * Returns newtworksecurities model
     *
     * @return Wsu_NewtworkSecurities_Model_Abstract
     */
    public function getNewtworkSecuritiesModel() {
        return Mage::helper('newtworksecurities')->getNewtworkSecurities($this->getFormId());
    }
}
