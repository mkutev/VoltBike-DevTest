<?php
namespace Magehit\Storelocator\Model;

class Photo extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Magehit\Storelocator\Model\ResourceModel\Photo');
    }
}
