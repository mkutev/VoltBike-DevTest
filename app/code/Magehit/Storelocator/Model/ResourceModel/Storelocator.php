<?php
namespace Magehit\Storelocator\Model\ResourceModel;

class Storelocator extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

	protected $_idFieldName = 'storelocator_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magehit_storelocator_storelocator', 'storelocator_id');
    }
}
