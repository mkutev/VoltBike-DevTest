<?php

namespace Magehit\Storepickup\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveStorepickupToOrderObserver implements ObserverInterface
{

    protected $_objectManager;
    protected $_serialize;
    protected $_timezone;
    public function __construct(
    \Magento\Framework\ObjectManagerInterface $objectmanager,
    \Magehit\Storepickup\Serialize\Serializer\Json $serialize,
    \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    )
    {
        $this->_objectManager = $objectmanager;
        $this->_serialize = $serialize;
        $this->_timezone = $timezone;
    }

    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        $quote = $quoteRepository->get($order->getQuoteId());
        if(!$quote->getStorepickupData()) return $this;
        $data = $this->_serialize->unserialize($quote->getStorepickupData());
       
        if($data && is_array($data)){
            $order->setStorepickupId($data['id']);
            // var_dump($data);return false;
            if($data['date']){
                /* $dateTimeZone = $this->_timezone->date(\DateTime::createFromFormat('d/m/Y H:i', $data['date'].' '.$data['time']))->format('m/d/y H:i:s'); */
                /* $dateTimeZone = $this->_timezone->date(\DateTime::createFromFormat('d/m/Y H:i', $data['date'].' 00:00:00'))->format('m/d/y H:i:s'); */
                $dateTimeZone =  \DateTime::createFromFormat('d/m/Y H:i:s', $data['date'] .' 00:00:00')->format('m/d/y H:i:s');
                $order->setStorepickupDatetime($dateTimeZone);
            }
            
            //$order->setStorepickupDatetime($newDate->format('m/d/y H:i:s'));
        }
        
        return $this;
    }

}