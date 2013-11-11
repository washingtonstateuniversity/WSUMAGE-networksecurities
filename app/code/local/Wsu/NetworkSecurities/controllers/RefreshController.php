<?php
class Wsu_NetworkSecurities_RefreshController extends Mage_Core_Controller_Front_Action {
    /**
     * Refreshes networksecurities and returns JSON encoded URL to image (AJAX action)
     * Example: {'imgSrc': 'http://example.com/media/networksecurities/67842gh187612ngf8s.png'}
     *
     * @return null
     */
    public function indexAction() {
        $formId = $this->getRequest()->getPost('formId');
        $networksecuritiesModel = Mage::helper('networksecurities')->getNetworkSecurities($formId);
        $this->getLayout()->createBlock($networksecuritiesModel->getBlockName())->setFormId($formId)->setIsAjax(true)->toHtml();
        $this->getResponse()->setBody(json_encode(array('imgSrc' => $networksecuritiesModel->getImgSrc())));
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
    }
}