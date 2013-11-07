<?php
class Wsu_NewtworkSecurities_Adminhtml_ErrorController extends Mage_Core_Controller_Front_Action {
	//not buying that this is the best place to do the check in the flow.
    public function indexAction() {		
        $checker = Mage::getModel('wsu_newtworksecurities/checker');
		echo $checker->httpbl_check_visitor();

		//die("got to the error in Adminhtml_ErrorController");
        $this->loadLayout();
        $this->renderLayout();
    }
}
