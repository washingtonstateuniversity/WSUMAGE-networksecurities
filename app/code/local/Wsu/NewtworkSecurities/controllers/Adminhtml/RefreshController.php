<?php

class Wsu_NewtworkSecurities_Adminhtml_RefreshController extends Mage_Adminhtml_Controller_Action {
    /**
     * Refreshes newtworksecurities and returns JSON encoded URL to image (AJAX action)
     * Example: {'imgSrc': 'http://example.com/media/newtworksecurities/67842gh187612ngf8s.png'}
     *
     * @return null
     */
    public function refreshAction() {
        $formId = $this->getRequest()->getPost('formId');
        $newtworksecuritiesModel = Mage::helper('newtworksecurities')->getNewtworkSecurities($formId);
        $this->getLayout()->createBlock($newtworksecuritiesModel->getBlockName())->setFormId($formId)->setIsAjax(true)->toHtml();
        $this->getResponse()->setBody(json_encode(array('imgSrc' => $newtworksecuritiesModel->getImgSrc())));
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
