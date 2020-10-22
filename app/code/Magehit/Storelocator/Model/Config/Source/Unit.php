<?php
namespace Magehit\Storelocator\Model\Config\Source;

class Unit implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 'mile', 'label' => __('mile')],
            ['value' => 'kilometer', 'label' => __('kilometer')]
        ];
    }
}