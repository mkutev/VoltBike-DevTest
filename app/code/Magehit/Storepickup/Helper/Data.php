<?php
namespace Magehit\Storepickup\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_objectManager;
	protected $_registry;
	protected $_storeManager;
	protected $_ruleFactory;
	protected $_pickupFactory;
	public $timeZone;
	protected $_serialize;
    public function __construct(Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magehit\Storepickup\Model\RuleFactory $ruleFactory,
		\Magehit\Storepickup\Model\StorepickupFactory $storepickupFactory,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone,
		\Magento\Framework\Serialize\Serializer\Json $serialize
    ) {
		$this->_objectManager 		= $objectManager;
        $this->_registry 			= $registry;
		$this->_storeManager 		= $storeManager;
		$this->_ruleFactory         = $ruleFactory;
		$this->_pickupFactory      = $storepickupFactory;
		$this->timeZone = $timeZone;
		$this->_serialize = $serialize;
        parent::__construct($context);
    }
	public function getListProductIdsInRule(){
		$_rule = $this->_ruleFactory->create()->getCollection();
		$_pickups = $this->_pickupFactory->create()->getCollection()->locationActive()->load();
		$array = [];
		foreach ($_pickups as $pickup) {
			$rule = $this->_ruleFactory->create()->load($pickup->getRuleId());
			$condeitons = $this->_serialize->unserialize($rule->getConditionsSerialized());
	        $rule->getConditions()->loadArray($condeitons);
	        $array[$pickup->getStorepickupId()] = $rule->getListProductIdsInRule();
		}
		return $array;
	}
	public function getInfoStore($id){
		$_pickup = $this->_pickupFactory->create()->load($id);
		$html = $_pickup->getStoreName();
		if($_pickup->getStreet()) $html .= ', '.$_pickup->getStreet();
		if($_pickup->getCity()) $html .= ', '.$_pickup->getCity();
		if($_pickup->getRegion()) $html .= ', '.$_pickup->getRegion();
		//if($_pickup->getRegion()) $html .= $_pickup->getPostcode();
		if($_pickup->getRegion()) $html .=', '.$_pickup->getCountry();
		return $html;
	}

	public function getStorebyproduct($id){
		$list= $this->getListProductIdsInRule();
		$result = [];
		if($list){
			foreach ($list as $key => $value) {
				if (in_array($id, $value)) {
					$result[] = $key;
				}
			}
			return $result;
		}
		return;
	}
	
	public function getStorePickupAvaiable($quote){
        $arr = $this->getListProductIdsInRule();
        $prd = array();
        $rs = array();
        if ($quote->getAllItems()) {
            foreach ($quote->getAllItems() as $item) {
                $prd[]=$item->getProduct()->getId();
            }
        }
        
        foreach ($arr as $key =>  $value) {
            if(array_intersect($value,$prd) != Null){
                $rs[] = $key;
            }
        }
        return $rs;
    }
	
	
	public function getDetailsStorePickupAvaiable($quote){
        $listoption = array();
		$pickupModel = $this->_pickupFactory->create();
		foreach ($this->getStorePickupAvaiable($quote) as $idItem) {
			$listoption[]= [
				'label'=>$pickupModel->load($idItem)->getStoreName(),
				'value'=>$idItem
			];
		}
		return $listoption;
    }

	public function formatMySqlDateTime($dateString, $format = false, $store = false)
    {
        if($dateString == '0000-00-00 00:00:00')
        {
            $result = 'N/A';
            return $result;
        }
        $format = 'F j, Y';//$format ? $format : $this->getConfigDateFormat();
        $store = $store ? $store : $this->_storeManager->getStore();
        return $this->timeZone
            ->scopeDate($store, $dateString, true)
            ->format($format);
    }
}
