<?php
namespace Magehit\Storepickup\Model\Config\Source\Backend;
class Day implements \Magento\Framework\Option\ArrayInterface
{
   public function toOptionArray()
    {
        return array(
            array(
                'value' => '-1',
                'label' => __('No Day')
            ),
            array(
                'value' => '0',
                'label' => __('Sunday')
            ),
            array(
                'value' => '1',
                'label' => __('Monday')
            ),
            array(
                'value' => '2',
                'label' => __('Tuesday')
            ),
            array(
                'value' => '3',
                'label' => __('Wednesday')
            )
            ,
            array(
                'value' => '4',
                'label' => __('Thursday')
            )
            ,
            array(
                'value' => '5',
                'label' => __('Friday')
            )
            ,
            array(
                'value' => '6',
                'label' => __('Saturday')
            )
            
        );
    }
}