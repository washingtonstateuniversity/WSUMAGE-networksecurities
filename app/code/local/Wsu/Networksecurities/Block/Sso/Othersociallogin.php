<?php
class Wsu_Networksecurities_Block_Sso_Othersociallogin extends Mage_Customer_Block_Account_Dashboard{

	public function __construct() {
		parent::__construct();
		$this->setTemplate('wsu/networksecurities/othersociallogin_block.phtml');
	}
	
	public function isShowFaceBookButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/facebook_login/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowGmailButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/gologin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowTwitterButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/twlogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowYahooButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/yalogin/is_active',Mage::app()->getStore()->getId());
	}		  
	
	public function getDirection() {
		return Mage::getStoreConfig('wsu_networksecurities/general_sso/direction',Mage::app()->getStore()->getId());
	}
	
	public function getIsActive() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/general_sso/is_active',Mage::app()->getStore()->getId());
	}	
	
	public function getFacebookButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_fblogin')
					->setTemplate('wsu/networksecurities/dashboard/bt_fblogin.phtml')->toHtml();
		
	}
	
	public function getGmailButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_gologin')
					->setTemplate('wsu/networksecurities/dashboard/bt_gologin.phtml')->toHtml();
	
	}

	public function getTwitterButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_twlogin')
					->setTemplate('wsu/networksecurities/dashboard/bt_twlogin.phtml')->toHtml();
		
	}

	public function getYahooButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_yalogin')
					->setTemplate('wsu/networksecurities/dashboard/bt_yalogin.phtml')->toHtml();
	}	

	public function isShowOpenButton() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/openlogin/is_active',Mage::app()->getStore()->getId());
    }
	
	public function getOpenButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_openlogin')
					->setTemplate('wsu/networksecurities/dashboard/bt_openlogin.phtml')->toHtml();
	}	
	
	public function isShowLjButton() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/ljlogin/is_active',Mage::app()->getStore()->getId());
    }
	
	public function getLjButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_ljlogin')
					->setTemplate('wsu/networksecurities/dashboard/bt_ljlogin.phtml')->toHtml();
	}	

	
	public function getLinkedButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_linkedlogin')
					->setTemplate('wsu/networksecurities/dashboard/bt_linkedlogin.phtml')->toHtml();
	}	
	
	public function isShowLinkedButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/linklogin/is_active',Mage::app()->getStore()->getId());
	}
	public function isShowAolButton() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/aollogin/is_active',Mage::app()->getStore()->getId());
    }
    
    public function isShowWpButton() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/wplogin/is_active',Mage::app()->getStore()->getId());
    }
	
	public function isShowCalButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/callogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowOrgButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/orglogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowFqButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/fqlogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowLiveButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/livelogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowMpButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/mplogin/is_active',Mage::app()->getStore()->getId());
	}
	
    public function getAolButton() {        
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_aollogin')
                ->setTemplate('wsu/networksecurities/dashboard/bt_aollogin.phtml')->toHtml();
    }
    
    public function getWpButton() {
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_wplogin')
                ->setTemplate('wsu/networksecurities/dashboard/bt_wplogin.phtml')->toHtml();
    }
    
    public function getAuWp() {        
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_wplogin')
                ->setTemplate('wsu/networksecurities/au_wp.phtml')->toHtml();
    }
    
	public function getCalButton() {
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_callogin')
                ->setTemplate('wsu/networksecurities/dashboard/bt_callogin.phtml')->toHtml();
    }
	
	public function getAuCal() {        
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_calllogin')
                ->setTemplate('wsu/networksecurities/au_cal.phtml')->toHtml();
    }
	
	public function getOrgButton() {
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_orglogin')
                ->setTemplate('wsu/networksecurities/dashboard/bt_orglogin.phtml')->toHtml();
    }
	
	public function getFqButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_fqlogin')
				->setTemplate('wsu/networksecurities/dashboard/bt_fqlogin.phtml')->toHtml();
	}
    
    public function getLiveButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_livelogin')
				->setTemplate('wsu/networksecurities/dashboard/bt_livelogin.phtml')->toHtml();
	}
	
	public function getMpButton() {	
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_mplogin')
				->setTemplate('wsu/networksecurities/dashboard/bt_mplogin.phtml')->toHtml();	
	}
	public function isShowPerButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/perlogin/is_active',Mage::app()->getStore()->getId());
	}
	public function getPerButton() {	
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_perlogin')
				->setTemplate('wsu/networksecurities/dashboard/bt_perlogin.phtml')->toHtml();	
	}
	public function isShowSeButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/selogin/is_active',Mage::app()->getStore()->getId());
	}
	public function getSeButton() {	
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_selogin')
				->setTemplate('wsu/networksecurities/dashboard/bt_selogin.phtml')->toHtml();	
	}
    protected function _beforeToHtml() {
		if(!$this->getIsActive()) {
		//	$this->setTemplate(null);
		}
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
		//	$this->setTemplate(null);
		}
		$this->getTemplate();
		if(Mage::registry('shown_sociallogin_button')) {
		//	$this->setTemplate(null);
		}elseif($this->getTemplate()) {
		//	Mage::register('shown_sociallogin_button',true);
		}
		
		return parent::_beforeToHtml();
	}	
	
	public function sortOrderFaceBook() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/facebook_login/sort_order');
	}
	
	public function sortOrderGmail() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/gologin/sort_order');
	}
	
	public function sortOrderTwitter() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/twlogin/sort_order');
	}
	
	public function sortOrderYahoo() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/yalogin/sort_order');
	}	
	
	public function sortOrderOpen() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/openlogin/sort_order');
    }
	
	public function sortOrderLj() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/ljlogin/sort_order');
    }
	
	public function sortOrderLinked() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/linklogin/sort_order');
	}
	
	public function sortOrderAol() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/aollogin/sort_order',Mage::app()->getStore()->getId());
    }
    
    public function sortOrderWp() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/wplogin/sort_order',Mage::app()->getStore()->getId());
    }
	
	public function sortOrderCal() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/callogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderOrg() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/orglogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderFq() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/fqlogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderLive() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/livelogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderMp() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/mplogin/sort_order',Mage::app()->getStore()->getId());
	}
	public function sortOrderPer() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/perlogin/sort_order',Mage::app()->getStore()->getId());
	}
	public function sortOrderSe() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/selogin/sort_order',Mage::app()->getStore()->getId());
	}
	public function makeArrayButton() {
		$buttonArray = array();
        if ($this->isShowFaceBookButton()){
			$buttonArray[] = array(
			'button'=>$this->getFacebookButton(),
			'check' =>$this->isShowFaceBookButton(),
			'id'	=> 'bt-loginfb',
			'sort'  => $this->sortOrderFaceBook()
			);
		}
        if ($this->isShowGmailButton()){
			$buttonArray[] = array(
			'button'=>$this->getGmailButton(),
			'check'=>$this->isShowGmailButton(),
			'id'	=> 'bt-logingo',
			'sort'=> $this->sortOrderGmail()
			);
		}
        if ($this->isShowTwitterButton()){
			$buttonArray[] = array(
			'button'=>$this->getTwitterButton(),
			'check'=>$this->isShowTwitterButton(),
			'id'	=> 'bt-logintw',
			'sort'=>$this->sortOrderTwitter()
			);
		}
        if ($this->isShowYahooButton()){
			$buttonArray[] = array(
			'button'=>$this->getYahooButton(),
			'check'=>$this->isShowYahooButton(),
			'id'	=> 'bt-loginya',
			'sort'=>$this->sortOrderYahoo()
			);
		}
        if ($this->isShowAolButton()){
			$buttonArray[] = array(
			'button'=>$this->getAolButton(),
			'check'=>$this->isShowAolButton(),
			'id'	=> 'bt-loginaol',
			'sort'=>$this->sortOrderAol()
			);
		}
        if ($this->isShowWpButton()){
			$buttonArray[] = array(
			'button'=>$this->getWpButton(),
			'check'=>$this->isShowWpButton(),
			'id'	=> 'bt-loginwp',
			'sort'=>$this->sortOrderWp()
			);
		}
        if ($this->isShowCalButton()){
			$buttonArray[] = array(
			'button'=>$this->getCalButton(),
			'check'=>$this->isShowCalButton(),
			'id'	=> 'bt-logincal',
			'sort'=>$this->sortOrderCal()
			);
		}
        if ($this->isShowOrgButton()){
			$buttonArray[] = array(
			'button'=>$this->getOrgButton(),
			'check'=>$this->isShowOrgButton(),
			'id'	=> 'bt-loginorg',
			'sort'=>$this->sortOrderOrg()
			);
		}
        if ($this->isShowFqButton()){
			$buttonArray[] = array(
			'button'=>$this->getFqButton(),
			'check'=>$this->isShowFqButton(),
			'id'	=> 'bt-loginfq',
			'sort'=>$this->sortOrderFq()
			);
		}
        if ($this->isShowLiveButton()){
			$buttonArray[] = array(
			'button'=>$this->getLiveButton(),
			'check'=>$this->isShowLiveButton(),
			'id'	=> 'bt-loginlive',
			'sort'=>$this->sortOrderLive()
			);
		}
        if ($this->isShowMpButton()){
			$buttonArray[] = array(
			'button'=>$this->getMpButton(),
			'check'=>$this->isShowMpButton(),
			'id'	=> 'bt-loginmp',
			'sort'=>$this->sortOrderMp()
			);
		}
        if ($this->isShowLinkedButton()){
			$buttonArray[] = array(
			'button'=>$this->getLinkedButton(),
			'check'=>$this->isShowLinkedButton(),
			'id'	=> 'bt-loginlinked',
			'sort'=>$this->sortOrderLinked()
			);
		}
        if ($this->isShowOpenButton()){
			$buttonArray[] = array(
			'button'=>$this->getOpenButton(),
			'check'=>$this->isShowOpenButton(),
			'id'	=> 'bt-loginopen',
			'sort'=>$this->sortOrderOpen()
			);
		}
        if ($this->isShowLjButton()){
			$buttonArray[] = array(
			'button'=>$this->getLjButton(),
			'check'=>$this->isShowLjButton(),
			'id'	=> 'bt-loginlj',
			'sort'=>$this->sortOrderLj()
			);
		}
		if ($this->isShowPerButton()){
			$buttonArray[] = array(
			'button'=>$this->getPerButton(),
			'check'=>$this->isShowPerButton(),
			'id'	=> 'bt-loginper',
			'sort'=>$this->sortOrderPer()
			);
		}
		if ($this->isShowSeButton()){
			$buttonArray[] = array(
			'button'=>$this->getSeButton(),
			'check'=>$this->isShowSeButton(),
			'id'	=> 'bt-loginse',
			'sort'=>$this->sortOrderSe()
			);
		}
		usort($buttonArray, array($this, 'compareSortOrder'));
		return $buttonArray;
	}
	
	public function compareSortOrder($a, $b) {
		if ($a['sort'] == $b['sort']) return 0;
		return $a['sort'] < $b['sort'] ? -1 : 1;
	}
	
	public function getNumberShow() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/general_sso/number_show',Mage::app()->getStore()->getId());
	}
}