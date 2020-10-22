<?php
namespace Magehit\Storepickup\Block;

class Ajax extends \Magento\Framework\View\Element\Template{
    
    protected $_StorepickupFactory;
    protected $dataHelper;
    protected $_countryFactory; 
    protected $_regionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
        \Magehit\Storepickup\Model\StorepickupFactory $StorepickupFactory,
        \Magehit\Storepickup\Helper\Data $dataHelper,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
        parent::__construct($context);
        $this->_StorepickupFactory = $StorepickupFactory;
        $this->dataHelper = $dataHelper;
        $this->_countryFactory = $countryFactory;
        $this->_regionFactory = $regionFactory;
    }
    public function getCurentStorepickup(){
        $storeid  = $this->getResponse();
        $data = '';
        if($storeid){
            $data = $this->_StorepickupFactory->create()->load($storeid);
        }
        
        return $data;

    }
    
    public function getName(){
        $name = '';
        if($this->getCurentStorepickup()){
            $name = $this->getCurentStorepickup()->getStoreName();
        }

        return $name;
    }
    public function getPhone(){
        $phone = '';
        if($this->getCurentStorepickup()){
            $phone = $this->getCurentStorepickup()->getTelephone();
        }

        return $phone;
    }
    public function getAddress(){
        $address = '';
        if($_store = $this->getCurentStorepickup()){
           if($_store->getStreet()) $address .= $_store->getStreet();
           if($_store->getCity()) $address .= ', '.$_store->getCity();
           if($_store->getRegionId()){
            $region_arr =$this->_regionFactory->create()->load($_store->getRegionId());
            $address .= ', '.$region_arr['name'];
           }elseif ($_store->getRegion()) {
               $address .= ', '.$_store->getRegion();
           }
           if($_store->getPostcode()) $address .= ', '.$_store->getPostcode();
           
           if($_store->getCountry()){
                $country = $this->_countryFactory->create()->loadByCode($_store->getCountry());
                if($country){
                    $address .= ', '.$country->getName();
                }else{
                    $address .= ', '.$_store->getCountry();
                }
            
           } 

        }

        return $address;
    }
    public function getFormattedPrice(){
        $price = 0;
        if($_store = $this->getCurentStorepickup()){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
            $price = $_store->getHandlingFee(); 
            $formatprice = $priceHelper->currency($price, true, false);
        }
        return $formatprice;
    }
}
