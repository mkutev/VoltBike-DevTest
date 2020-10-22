<?php
namespace Magehit\Storelocator\Block;
class Search extends \Magento\Framework\View\Element\Template
{
	protected $objectManager;
	protected $dataHelper;
	protected $scopeConfig;
	protected $_registry;
	protected $model;
	protected $token = null;
	// protected $_storeManager;

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magehit\Storelocator\Model\StorelocatorFactory $model,
        \Magehit\Storelocator\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        // \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->objectManager = $objectmanager;
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_registry = $registry;
        $this->model = $model;
        // $this->_storeManager = $storeManager;
        parent::__construct($context,$data);
    }
    public function getlistStore()
    {
        return $this->_registry->registry('storelocator_model');
    }
    public function getHelper(){
        return $this->dataHelper;
    }
    
    public function isEnable(){
    	return $this->dataHelper->getEnable($this->_storeManager->getStore()->getId());
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
