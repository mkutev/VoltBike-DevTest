<?php

namespace Magehit\Storelocator\Ui\Component\Listing\Column;;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'store_thumnail';

    const ALT_FIELD = 'name';
    protected $_assetRepo;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magehit\Storelocator\Helper\Data $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
         \Magento\Framework\View\Asset\Repository $assetRepo,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->_assetRepo = $assetRepo;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {

            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $_store_item = new \Magento\Framework\DataObject($item);
                if(trim($_store_item->getStore_thumnail()) != ''){
                    $item[$fieldName . '_src'] = $this->imageHelper->getUrlimage($_store_item->getStore_thumnail());
                    $item[$fieldName . '_alt'] = $this->getAlt($item) ?: $fieldName;
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'magehit_storelocator/storelocator/edit',
                        ['storelocator_id' => $_store_item->getStorelocator_id(), 'store' => $this->context->getRequestParam('store')]
                    );
                   
                    $item[$fieldName . '_orig_src'] = $this->imageHelper->getUrlimage($_store_item->getStore_thumnail());
                }else{
                    $item[$fieldName . '_src'] =$this->_assetRepo->getUrl('Magehit_Storelocator::images/thumbnail.jpg');
                    $item[$fieldName . '_alt'] = $this->getAlt($item) ?: $fieldName;
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'magehit_storelocator/storelocator/edit',
                        ['storelocator_id' => $_store_item->getStorelocator_id(), 'store' => $this->context->getRequestParam('store')]
                    );
                   
                    $item[$fieldName . '_orig_src'] = $this->_assetRepo->getUrl('Magehit_Storelocator::images/thumbnail.jpg');
                }
            }
        }

        return $dataSource;
    }

}
