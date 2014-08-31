<?php
class Wsu_Networksecurities_Model_Sso_Source_Ssooptions {
    public function toOptionArray() {
        $helper = Mage::helper('wsu_networksecurities');
        return array(
            array('value'=>'facebook', 'label'=> $helper->__('Facebook')),
            array('value'=>'twitter', 'label'=> $helper->__('Twitter')),
            array('value'=>'google', 'label'=> $helper->__('Google')),
            array('value'=>'linkedin', 'label'=> $helper->__('LinkedIn')),
            array('value'=>'yahoo', 'label'=> $helper->__('Yahoo')),
			array('value'=>'wordpress', 'label'=> $helper->__('WordPress')),
			array('value'=>'myopenid', 'label'=> $helper->__('MyOpenId')),
			array('value'=>'livejournal', 'label'=> $helper->__('Livejournal')),
			array('value'=>'clavid', 'label'=> $helper->__('Clavid')),
			array('value'=>'orange', 'label'=> $helper->__('Orange')),
			array('value'=>'foursquare', 'label'=> $helper->__('Foursquare')),
			array('value'=>'live', 'label'=> $helper->__('Windows Live')),
			array('value'=>'myspace', 'label'=> $helper->__('MySpace')),
			array('value'=>'persona', 'label'=> $helper->__('Persona')),
			array('value'=>'stackexchange', 'label'=> $helper->__('Stack Exchange')),
        );
    }
}
