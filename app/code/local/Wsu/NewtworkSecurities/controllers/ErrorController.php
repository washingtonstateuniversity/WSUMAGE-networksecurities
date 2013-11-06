<?php

class Wsu_NewtworkSecurities_ErrorController extends Mage_Core_Controller_Front_Action {
    public function indexAction() {
		die("got to the error in _ErrorController");
        $this->loadLayout();
        $this->renderLayout();
    }
}
