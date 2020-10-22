<?php
namespace Magehit\Storepickup\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Region extends Column
{
    protected $_regionFactory;
    public function __construct(
        \Magento\Directory\Model\RegionFactory $regionFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_regionFactory = $regionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                if ($items['region']) {
                    $items['region'] = $items['region'];
                } elseif($items['region_id']) {
                    $region_arr = $this->_regionFactory->create()->load($items['region_id']);
                    $items['region'] = $region_arr['name'];
                   
                }
            }
        }
        return $dataSource;
    }

    
}
