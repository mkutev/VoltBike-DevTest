<?php


namespace Magehit\Storepickup\Model\ResourceModel\Storepickup;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'storepickup_id';
    protected function _construct()
    {
        $this->_init(
            'Magehit\Storepickup\Model\Storepickup',
            'Magehit\Storepickup\Model\ResourceModel\Storepickup'
        );
    }
    public function locationActive(){
    	$om = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
        $storeID = $storeManager->getStore()->getId();
        $this->getSelect()->where('main_table.status = 1');
        $this->getSelect()->where('find_in_set(?, in_store) OR find_in_set(0, in_store)', (int)$storeID);
        return $this;

    }
}
