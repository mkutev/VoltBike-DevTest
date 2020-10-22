<?php
namespace Magehit\Storepickup\Model\Plugin\Checkout;

class ShippingInformationManagementPlugin
{

    protected $quoteRepository;
    protected $_serialize;
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
         \Magehit\Storepickup\Serialize\Serializer\Json $serialize
    ) {
        $this->quoteRepository = $quoteRepository;
         $this->_serialize = $serialize;
    }

    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        if($extAttributes){
            $Data = [
            'id'   => $extAttributes->getStorepickupStore(),
            'date' => $extAttributes->getStorepickupDate(),
            'time' => $extAttributes->getStorepickupTime()
            ];
       
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setStorepickupData($this->_serialize->serialize($Data));
        }
        
    }
}