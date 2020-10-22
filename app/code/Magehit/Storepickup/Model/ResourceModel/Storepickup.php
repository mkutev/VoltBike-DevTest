<?php


namespace Magehit\Storepickup\Model\ResourceModel;

class Storepickup extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected $_idFieldName = 'storepickup_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magehit_storepickup_storepickup', 'storepickup_id');
    }
}
