<?php
class Wsu_Networksecurities_Block_Sso_Toplinks extends Mage_Core_Block_Template {
	
	
	/*
	 * @todo remeber to come back and dry this up
	*/
	
	
	
	
	public function __construct() {
		parent::__construct();		
		//$this->setTemplate('wsu/networksecurities/sociallogin_buttons.phtml');
	}
	
	public function isShowFaceBookButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/facebook_login/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowGmailButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/googlelogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowTwitterButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/twitterlogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowYahooButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/yahoologin/is_active',Mage::app()->getStore()->getId());
	}		  
	
	public function getDirection() {
		return Mage::getStoreConfig('wsu_networksecurities/general_sso/direction',Mage::app()->getStore()->getId());
	}
	
	public function getIsActive() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/general_sso/is_active',Mage::app()->getStore()->getId());
	}	
	
	public function getFacebookButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_fblogin')
					->setTemplate('wsu/networksecurities/toplinks/bt_fblogin.phtml')->toHtml();
		
	}
	
	public function getGmailButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_googlelogin')
					->setTemplate('wsu/networksecurities/toplinks/bt_googlelogin.phtml')->toHtml();
	
	}

	public function getTwitterButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_twitterlogin')
					->setTemplate('wsu/networksecurities/toplinks/bt_twitterlogin.phtml')->toHtml();
		
	}

	public function getYahooButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_yahoologin')
					->setTemplate('wsu/networksecurities/toplinks/bt_yahoologin.phtml')->toHtml();
	}	

	public function isShowOpenButton() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/myopenidlogin/is_active',Mage::app()->getStore()->getId());
    }
	
	public function getOpenButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_myopenidlogin')
					->setTemplate('wsu/networksecurities/toplinks/bt_myopenidlogin.phtml')->toHtml();
	}	
	
	public function isShowLjButton() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/livejournallogin/is_active',Mage::app()->getStore()->getId());
    }
	
	public function getLjButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_livejournallogin')
					->setTemplate('wsu/networksecurities/toplinks/bt_livejournallogin.phtml')->toHtml();
	}	

	
	public function getLinkedButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_linkedinlogin')
					->setTemplate('wsu/networksecurities/toplinks/bt_linkedinlogin.phtml')->toHtml();
	}	
	
	public function isShowLinkedButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/linkedin_login/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowAolButton() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/aollogin/is_active',Mage::app()->getStore()->getId());
    }
    
    public function isShowWpButton() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/wordpresslogin/is_active',Mage::app()->getStore()->getId());
    }
	
	public function isShowCalButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/clavidlogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowOrgButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/orangelogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowFqButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/fqlogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowLiveButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/livelogin/is_active',Mage::app()->getStore()->getId());
	}
	
	public function isShowMpButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/myspacelogin/is_active',Mage::app()->getStore()->getId());
	}
	
    public function getAolButton() {        
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_aollogin')
                ->setTemplate('wsu/networksecurities/toplinks/bt_aollogin.phtml')->toHtml();
    }
    
    public function getWpButton() {
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_wordpresslogin')
                ->setTemplate('wsu/networksecurities/toplinks/bt_wordpresslogin.phtml')->toHtml();
    }
    
    public function getAuWp() {        
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_wordpresslogin')
                ->setTemplate('wsu/networksecurities/toplinks/au_wp.phtml')->toHtml();
    }
    
	public function getCalButton() {
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_clavidlogin')
                ->setTemplate('wsu/networksecurities/toplinks/bt_clavidlogin.phtml')->toHtml();
    }
	
	public function getAuCal() {        
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_calllogin')
                ->setTemplate('wsu/networksecurities/toplinks/au_cal.phtml')->toHtml();
    }
	
	public function getOrgButton() {
        return $this->getLayout()->createBlock('wsu_networksecurities/sso_orangelogin')
                ->setTemplate('wsu/networksecurities/toplinks/bt_orangelogin.phtml')->toHtml();
    }
	
	public function getFqButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_fqlogin')
				->setTemplate('wsu/networksecurities/toplinks/bt_fqlogin.phtml')->toHtml();
	}
    
    public function getLiveButton() {
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_livelogin')
				->setTemplate('wsu/networksecurities/toplinks/bt_livelogin.phtml')->toHtml();
	}
	
	public function getMpButton() {	
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_myspacelogin')
				->setTemplate('wsu/networksecurities/toplinks/bt_myspacelogin.phtml')->toHtml();	
	}

	public function isShowPerButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/personalogin/is_active',Mage::app()->getStore()->getId());
	}
	public function getPerButton() {	
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_personalogin')
				->setTemplate('wsu/networksecurities/toplinks/bt_personalogin.phtml')->toHtml();	
	}
	public function isShowSeButton() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/selogin/is_active',Mage::app()->getStore()->getId());
	}
	public function getSeButton() {	
		return $this->getLayout()->createBlock('wsu_networksecurities/sso_selogin')
				->setTemplate('wsu/networksecurities/toplinks/bt_selogin.phtml')->toHtml();	
	}

	
    protected function _beforeToHtml() {
		if(!$this->getIsActive()) {
			$this->setTemplate(null);
		}
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->setTemplate(null);
		}
		$check = Mage::helper('wsu_networksecurities/customer')->getShownPositions();
		if (!in_array("popup", $check)) {
			$this->setTemplate(null);
		}
		return parent::_beforeToHtml();
	}	
	
	public function sortOrderFaceBook() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/facebook_login/sort_order');
	}
	
	public function sortOrderGmail() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/googlelogin/sort_order');
	}
	
	public function sortOrderTwitter() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/twitterlogin/sort_order');
	}
	
	public function sortOrderYahoo() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/yahoologin/sort_order');
	}	
	
	public function sortOrderOpen() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/myopenidlogin/sort_order');
    }
	
	public function sortOrderLj() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/livejournallogin/sort_order');
    }
	
	public function sortOrderLinked() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/linkedin_login/sort_order');
	}
	
	public function sortOrderAol() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/aollogin/sort_order',Mage::app()->getStore()->getId());
    }
    
    public function sortOrderWp() {
        return (int) Mage::getStoreConfig('wsu_networksecurities/wordpresslogin/sort_order',Mage::app()->getStore()->getId());
    }
	
	public function sortOrderCal() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/clavidlogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderOrg() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/orangelogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderFq() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/fqlogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderLive() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/livelogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderMp() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/myspacelogin/sort_order',Mage::app()->getStore()->getId());
	}
	
	public function sortOrderPer() {
		return (int) Mage::getStoreConfig('wsu_networksecurities/personalogin/sort_order',Mage::app()->getStore()->getId());
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
			'id'	=> 'bt-loginfb-popup',
			'sort'  => $this->sortOrderFaceBook()
			);
		}
        if ($this->isShowGmailButton()){
			$buttonArray[] = array(
			'button'=>$this->getGmailButton(),
			'check'=>$this->isShowGmailButton(),
			'id'	=> 'bt-logingo-popup',
			'sort'=> $this->sortOrderGmail()
			);
		}
        if ($this->isShowTwitterButton()){
			$buttonArray[] = array(
			'button'=>$this->getTwitterButton(),
			'check'=>$this->isShowTwitterButton(),
			'id'	=> 'bt-logintw-popup',
			'sort'=>$this->sortOrderTwitter()
			);
		}
        if ($this->isShowYahooButton()){
			$buttonArray[] = array(
			'button'=>$this->getYahooButton(),
			'check'=>$this->isShowYahooButton(),
			'id'	=> 'bt-loginya-popup',
			'sort'=>$this->sortOrderYahoo()
			);
		}
        if ($this->isShowAolButton()){
			$buttonArray[] = array(
			'button'=>$this->getAolButton(),
			'check'=>$this->isShowAolButton(),
			'id'	=> 'bt-loginaol-popup',
			'sort'=>$this->sortOrderAol()
			);
		}
        if ($this->isShowWpButton()){
			$buttonArray[] = array(
			'button'=>$this->getWpButton(),
			'check'=>$this->isShowWpButton(),
			'id'	=> 'bt-loginwp-popup',
			'sort'=>$this->sortOrderWp()
			);
		}
        if ($this->isShowCalButton()){
			$buttonArray[] = array(
			'button'=>$this->getCalButton(),
			'check'=>$this->isShowCalButton(),
			'id'	=> 'bt-logincal-popup',
			'sort'=>$this->sortOrderCal()
			);
		}
        if ($this->isShowOrgButton()){
			$buttonArray[] = array(
			'button'=>$this->getOrgButton(),
			'check'=>$this->isShowOrgButton(),
			'id'	=> 'bt-loginorg-popup',
			'sort'=>$this->sortOrderOrg()
			);
		}
        if ($this->isShowFqButton()){
			$buttonArray[] = array(
			'button'=>$this->getFqButton(),
			'check'=>$this->isShowFqButton(),
			'id'	=> 'bt-loginfq-popup',
			'sort'=>$this->sortOrderFq()
			);
		}
        if ($this->isShowLiveButton()){
			$buttonArray[] = array(
			'button'=>$this->getLiveButton(),
			'check'=>$this->isShowLiveButton(),
			'id'	=> 'bt-loginlive-popup',
			'sort'=>$this->sortOrderLive()
			);
		}
        if ($this->isShowMpButton()){
			$buttonArray[] = array(
			'button'=>$this->getMpButton(),
			'check'=>$this->isShowMpButton(),
			'id'	=> 'bt-loginmp-popup',
			'sort'=>$this->sortOrderMp()
			);
		}
        if ($this->isShowLinkedButton()){
			$buttonArray[] = array(
			'button'=>$this->getLinkedButton(),
			'check'=>$this->isShowLinkedButton(),
			'id'	=> 'bt-loginlinked-popup',
			'sort'=>$this->sortOrderLinked()
			);
		}
        if ($this->isShowOpenButton()){
			$buttonArray[] = array(
			'button'=>$this->getOpenButton(),
			'check'=>$this->isShowOpenButton(),
			'id'	=> 'bt-loginopen-popup',
			'sort'=>$this->sortOrderOpen()
			);
		}
        if ($this->isShowLjButton()){
			$buttonArray[] = array(
			'button'=>$this->getLjButton(),
			'check'=>$this->isShowLjButton(),
			'id'	=> 'bt-loginlj-popup',
			'sort'=>$this->sortOrderLj()
			);
		}
		if ($this->isShowPerButton()){
			$buttonArray[] = array(
			'button'=>$this->getPerButton(),
			'check'=>$this->isShowPerButton(),
			'id'	=> 'bt-loginper-popup',
			'sort'=>$this->sortOrderPer()
			);
		}
		if ($this->isShowSeButton()){
			$buttonArray[] = array(
			'button'=>$this->getSeButton(),
			'check'=>$this->isShowSeButton(),
			'id'	=> 'bt-loginse-popup',
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