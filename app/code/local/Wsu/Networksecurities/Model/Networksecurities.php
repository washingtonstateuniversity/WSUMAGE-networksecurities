<?php
class Wsu_Networksecurities_Model_Networksecurities extends Mage_Core_Model_Abstract {
	/*const CONFIG_CACHE_ID = 'wsu_networksecurities_config';
    protected $_config;
    protected function _construct( ) {
        $this->_initConfig();
    }
    protected function _initConfig( ) {
        $cacheId = self::CONFIG_CACHE_ID;
        $data    = Mage::app()->loadCache( $cacheId );
        if ( false !== $data ) {
            $data = unserialize( $data );
        }else{ $xml  = Mage::getConfig()->loadModulesConfiguration( 'networksecurities.xml' )->getNode();
            $data = $xml->asArray();
            Mage::app()->saveCache( serialize( $data ), $cacheId );
        }
        $this->_config = $data;
        return $this;
    }*/
}