<?php
class Wsu_Networksecurities_Block_Sso_Form_Mapper extends Varien_Data_Form_Element_Abstract {


    public function getElementHtml() {
        $elementAttributeHtml = '';
		$html="<h3>fooo</h3><br/>";
       /* if ($this->getReadonly()) {
            $elementAttributeHtml = $elementAttributeHtml . ' readonly="readonly"';
        }

        if ($this->getDisabled()) {
            $elementAttributeHtml = $elementAttributeHtml . ' disabled="disabled"';
        }

        $html = '<select onchange="toggleApplyVisibility(this)"' . $elementAttributeHtml . '>'
              . '<option value="0">' . $this->getModeLabels('all'). '</option>'
              . '<option value="1" ' . ($this->getValue()==null ? '' : 'selected') . '>' . $this->getModeLabels('custom'). '</option>'
              . '</select><br /><br />';*/

        $html .= parent::getElementHtml();
        return $html;
    }

    /**
     * Dublicate interface of Varien_Data_Form_Element_Abstract::setReadonly
     *
     * @param bool $readonly
     * @param bool $useDisabled
     * @return Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Apply
    
    public function setReadonly($readonly, $useDisabled = false) {
        $this->setData('readonly', $readonly);
        $this->setData('disabled', $useDisabled);
        return $this;
    }
 */
    public function getAfterElementHtml(){
        $html = parent::getAfterElementHtml();
        return $html." <h4>bar</h4> <script>
                        $('".$this->getHtmlId()."').disable();
                        </script>";
    }


}
