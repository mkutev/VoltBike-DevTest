<?php
namespace Magehit\Storelocator\Model\ResourceModel\Storelocator;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $helperData;
     /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
     /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    protected $_idFieldName = 'storelocator_id';

    public function __construct(
        \Magehit\Storelocator\Helper\Data $helperData,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->helperData = $helperData;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scope;
        $this->_objectManager = $objectManager;
        $this->_request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magehit\Storelocator\Model\Storelocator',
            'Magehit\Storelocator\Model\ResourceModel\Storelocator'

        );
    }
    public function locationFilters()
    {
        $center_lat = (float)$this->_request->getParam('lat');
        $center_lng = (float)$this->_request->getParam('lng');
        $radius = $this->_request->getParam('radius');
        $storeID = $this->_storeManager->getStore()->getId();
        $this->getSelect()->where('main_table.status = 1');
        $this->getSelect()->where('find_in_set(?, in_store) OR find_in_set(0, in_store)', (int)$storeID);
        if (isset($radius)) {
           $this->getSelect()->columns(
                array('distance' => 'SQRT(POW(69.1 * (main_table.lat - ' . $center_lat . '), 2) + POW(69.1 * (' . $center_lng . ' - main_table.lng) * COS(main_table.lat / 57.3), 2))')
            );
            if($this->helperData->getConfig('radius/radius_unit',$storeID) !='mile'){
                $radius = $radius / 1.609344;
            }
            $this->getSelect()->having('distance < ' . $radius)->order('distance','ASC');
        }
        return $this;
    }
    public function locationActive(){
        $storeID = $this->_storeManager->getStore()->getId();
        $this->getSelect()->where('main_table.status = 1');
        $this->getSelect()->where('find_in_set(?, in_store) OR find_in_set(0, in_store)', (int)$storeID);
        //var_dump( $this->getSelect()->__toString());die;
        return $this;

    }
    public function  productFilters($id)
    {
        $storeID = $this->_storeManager->getStore()->getId();
        $this->getSelect()->where('main_table.status = 1');
        $this->getSelect()->where('find_in_set(?, in_store) OR find_in_set(0, in_store)', (int)$storeID);
        $this->getSelect()->where('find_in_set(?, product_ids)', (int)$id);
        return $this;
    }
}
