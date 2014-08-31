<?php

class Wsu_Networksecurities_Controller_Sso_Abstract extends Mage_Core_Controller_Front_Action {
	public function makeCustomerData($user_info) {
		$data = array();
		$frist_name = isset($user_info['namePerson/first'])?$user_info['namePerson/first']:"";
		$last_name = isset($user_info['namePerson/last'])?$user_info['namePerson/last']:"";
		$email = $user_info['contact/email'];
		
		if(!$frist_name || !$last_name) {
			if(isset($user_info['namePerson/friendly'])) {
				$frist_name = $user_info['namePerson/friendly'] ; 
				$last_name = $user_info['namePerson/friendly'];
				$data['username']=$user_info['namePerson/friendly'];
			}else{ $emailpart = explode("@", $email);
				$frist_name = $emailpart['0'];
				$last_name  = $emailpart['0'];
				$data['username']=$email;
			}                   
		}
		
		$data['provider']="aol";
		$data['email']=$email;
		$data['firstname']=$frist_name;
		$data['lastname']=$last_name;

		return $data;
	}
}