<?php
namespace Magehit\Storelocator\Block;
class Detail extends \Magento\Framework\View\Element\Template
{
	protected $objectManager;
	protected $dataHelper;
	protected $scopeConfig;
	protected $_registry;
	protected $model;
	//protected $_storeManager;
    protected $_filterProvider;
    protected $_Customer;
    protected $timezoneInterface;

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magehit\Storelocator\Model\StorelocatorFactory $model,
        \Magehit\Storelocator\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        // \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Customer\Model\Session $Customer,
        array $data = []
    ) {
        $this->objectManager = $objectmanager;
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_registry = $registry;
        $this->model = $model;
        //$this->_storeManager = $storeManager;
        $this->_filterProvider = $filterProvider;
        $this->_Customer = $Customer;
        $this->timezoneInterface = $context->getLocaleDate();
        parent::__construct($context,$data);
    }
    public function getTimezone(){
        return $this->timezoneInterface;
    }
    public function getHelper(){
        return $this->dataHelper;
    }
    
    public function isEnable(){
    	return $this->dataHelper->getEnable($this->_storeManager->getStore()->getId());
    }
    public function getConfig($code){
        return $this->dataHelper->getConfig($code,$this->_storeManager->getStore()->getId());
    }
    public function getcurrentStore()
    {
        return $this->_registry->registry('storelocator_data');
    }
    public function getBack(){
       return $this->dataHelper->getLink();
    }
    public function getAddress()
    {
        $_store = $this->getcurrentStore();
        
        return $_store->getStreet(). ', ' .
            $_store->getCity() . ', ' . $_store->getRegion(). ' ' . $_store->getPostcode() . ', ' .
            $_store->getCountry();
    }
    public function getContent(){
        $_store = $this->getcurrentStore();
        return $this->_filterProvider->getBlockFilter()
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->filter($_store->getContent());
    }
    public function getCustomerName(){
        if ($this->_Customer->isLoggedIn()) {
           
            return $this->_Customer->getCustomer()->getName();  // get  Full Names
        }
        return '';
    }
    public function getCustomerEmail(){
        if ($this->_Customer->isLoggedIn()) {
            return $this->_Customer->getCustomer()->getEmail(); // get Email
        }
        return '';
    }
    public function getCurrentUrl() {
        return $this->_storeManager->getStore()->getCurrentUrl();
    }
    public function getCenterLat(){
      return  $this->dataHelper->getConfig('map/center_lat' ,$this->_storeManager->getStore()->getId());
    }
    public function getCenterLng(){
       return $this->dataHelper->getConfig('map/center_lng' ,$this->_storeManager->getStore()->getId());
    }
    public function getInitialZoom(){
      return  $this->dataHelper->getConfig('map/initial_zoom' ,$this->_storeManager->getStore()->getId());
    }
    public function getSearchResultZoom(){
      return  $this->dataHelper->getConfig('map/search_result_zoom' ,$this->_storeManager->getStore()->getId());
    }
    public function getRadiusUnit(){
       return  $this->dataHelper->getConfig('radius/radius_unit' ,$this->_storeManager->getStore()->getId());
    }
    public function getMapjs(){
       return  'https://maps.googleapis.com/maps/api/js?key='.trim($this->dataHelper->getConfig('map/api_key' ,$this->_storeManager->getStore()->getId()));
    }
}
