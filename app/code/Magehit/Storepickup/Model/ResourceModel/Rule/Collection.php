<?php
 
namespace Magehit\Storepickup\Model\ResourceModel\Rule;
 
class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{

    protected $date;
    protected $_idFieldName = 'rule_id';
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
 
    /**
     * Set resource model and determine field mapping
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magehit\Storepickup\Model\Rule', 'Magehit\Storepickup\Model\ResourceModel\Rule');
    }
 
    
   
}
