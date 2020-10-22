<?php
namespace Magehit\Storepickup\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Country extends Column
{
    protected $countryFactory;
    public function __construct(
        \Magento\Directory\Model\CountryFactory $countryFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->countryFactory = $countryFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                if ($items['country']) {
                    $country = $this->countryFactory->create()->loadByCode($items['country']);
                    if($country){
                        $items['country'] = $country->getName();
                    }else{
                       
                    }
                } 
            }
        }
        return $dataSource;
    }

    
}
