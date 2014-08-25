<?php
/**
 * Twitter API 1.1
 */
class Wsu_Networksecurities_Model_Sso_Twitter extends Zend_Service_Twitter {
	/**
     * Show extended information on a user
     *
     * @param  int|string $id User ID or name
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return stdClass
     */
    public function userShow($id) {
        $this->_init();
        $path = '1.1/users/show.json';
        $response = $this->_get($path, array('id'=>$id));
		return Zend_Json::decode($response->getBody(), Zend_Json::TYPE_OBJECT);
    }
}
