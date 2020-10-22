<?php

namespace Magehit\Storepickup\Plugin\Carrier;

use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;
use Magehit\Storepickup\Model\Carrier\Storepickup;

class StorepickupData
{
    protected $_coreRegistry;
    protected $extensionFactory;

    protected $_storepickup;

    public function __construct(\Magento\Framework\Registry $registry,ShippingMethodExtensionFactory $extensionFactory, Storepickup $torepickup)
    {
        $this->_coreRegistry = $registry;
        $this->extensionFactory = $extensionFactory;
        $this->_storepickup = $torepickup;
    }

    
    public function afterModelToDataObject(ShippingMethodConverter $subject, ShippingMethodInterface $result)
    {
        $extensibleAttribute =  ($result->getExtensionAttributes())
            ? $result->getExtensionAttributes()
            : $this->extensionFactory->create();
        $kq= '';

        if($result->getMethodCode() == 'storepickup' && $this->_coreRegistry->registry('list_storepickup')){
            $kq = $this->_coreRegistry->registry('list_storepickup');
            
        }  
         $extensibleAttribute->setStorepickupId($kq);
         $result->setExtensionAttributes($extensibleAttribute);

        
        return $result;
    }
}