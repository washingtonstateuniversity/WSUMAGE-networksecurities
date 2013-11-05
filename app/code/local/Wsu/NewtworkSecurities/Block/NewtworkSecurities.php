<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Wsu_NewtworkSecurities
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * NewtworkSecurities block
 *
 * @category   Core
 * @package    Wsu_NewtworkSecurities
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Wsu_NewtworkSecurities_Block_NewtworkSecurities extends Mage_Core_Block_Template {
	protected function _construct(){
        parent::_construct();
    }
    /**
     * Renders newtworksecurities HTML (if required)
     *
     * @return string
     */
    protected function _toHtml() {
        $blockPath = Mage::helper('newtworksecurities')->getNewtworkSecurities($this->getFormId())->getBlockName();
        $block = $this->getLayout()->createBlock($blockPath);
        $block->setData($this->getData());
        return $block->toHtml();
    }
	
    protected $_template = 'wsu/newtworksecurities/honeypot.phtml';



    public function getHoneypotName(){
        /* @var $helper Wsu_newtworksecurities_Helper_Data */
        $helper = Mage::helper('wsu_newtworksecurities');
        return $helper->getHoneypotName();
    }
	
	
}
