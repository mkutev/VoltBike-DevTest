<?php

namespace Magehit\Customevent\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ConvertToOrder implements ObserverInterface
{
    protected $_objectManager;
    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectmanager
    )
    {
        $this->_objectManager = $objectmanager;
    }

    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        $quote = $quoteRepository->get($order->getQuoteId());
        if(!$quote->getPresenter()) return $this;
        $presenter	 = $quote->getPresenter();
        $ambassador  = $quote->getAmbassador();
       
		$order->setPresenter($presenter);
		$order->setAmbassador($ambassador);
		
        return $this;
    }

}