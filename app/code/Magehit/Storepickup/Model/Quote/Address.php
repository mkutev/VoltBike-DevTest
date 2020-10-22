<?php

namespace Magehit\Storepickup\Model\Quote;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Quote\Api\Data\AddressInterface;


class Address extends \Magento\Quote\Model\Quote\Address
{
    protected $_serialize;
    public function __construct(
        \Magehit\Storepickup\Serialize\Serializer\Json $serialize,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        AddressMetadataInterface $metadataService,
        AddressInterfaceFactory $addressDataFactory,
        RegionInterfaceFactory $regionDataFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\ItemFactory $addressItemFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Address\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Quote\Model\Quote\Address\RateFactory $addressRateFactory,
        \Magento\Quote\Model\Quote\Address\RateCollectorInterfaceFactory $rateCollector,
        \Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory $rateCollectionFactory,
        \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory,
        \Magento\Quote\Model\Quote\Address\Total\CollectorFactory $totalCollectorFactory,
        \Magento\Quote\Model\Quote\Address\TotalFactory $addressTotalFactory,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Shipping\Model\CarrierFactoryInterface $carrierFactory,
        \Magento\Quote\Model\Quote\Address\Validator $validator,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Quote\Model\Quote\Address\CustomAttributeListInterface $attributeList,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Magento\Quote\Model\Quote\TotalsReader $totalsReader,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
        
    ) {
        $this->_serialize = $serialize;
        parent::__construct(
        $context,
        $registry,
        $extensionFactory,
        $customAttributeFactory,
        $directoryData,
        $eavConfig,
        $addressConfig,
        $regionFactory,
        $countryFactory,
        $metadataService,
        $addressDataFactory,
        $regionDataFactory,
        $dataObjectHelper,
        $scopeConfig,
        $addressItemFactory,
        $itemCollectionFactory,
        $addressRateFactory,
        $rateCollector,
        $rateCollectionFactory,
        $rateRequestFactory,
        $totalCollectorFactory,
        $addressTotalFactory,
        $objectCopyService,
        $carrierFactory,
        $validator,
        $addressMapper,
        $attributeList,
        $totalsCollector,
        $totalsReader,
        $resource,
        $resourceCollection,
        $data
        );
    }


    public function requestShippingRates(\Magento\Quote\Model\Quote\Item\AbstractItem $item = null)
    {
        /** @var $request \Magento\Quote\Model\Quote\Address\RateRequest */
        $request = $this->_rateRequestFactory->create();
        if($this->getQuote()->getStorepickupData()){
            $id_pickup = $this->_serialize->unserialize($this->getQuote()->getStorepickupData());
            //if($this->getQuote()->getStorepickupData())
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('--aa--');
            if($id_pickup) $request->setPickupId($id_pickup['id']);
        }
        return parent::requestShippingRates($item);
    }

}