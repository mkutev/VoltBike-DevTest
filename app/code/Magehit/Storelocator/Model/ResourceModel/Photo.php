<?php
namespace Magehit\Storelocator\Model\ResourceModel;

class Photo extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

	protected $_idFieldName = 'photo_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magehit_storelocator_photo', 'photo_id');
    }
}
