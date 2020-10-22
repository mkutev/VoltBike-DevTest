<?php
 
namespace Magehit\Storelocator\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storelocator\Model\StorelocatorFactory;
class Getinstagramimage extends \Magento\Framework\App\Action\Action
{
    protected $_rawResultFactory;
    protected $_StorelocatorFactory;
    protected $dataHelper;
    public function __construct(
        Context $context,
        StorelocatorFactory $StorelocatorFactory,
        \Magento\Framework\Controller\Result\RawFactory $rawResultFactory,
        \Magehit\Storelocator\Helper\Data $dataHelper
    )
    {
        $this->_rawResultFactory = $rawResultFactory;
        $this->_StorelocatorFactory = $StorelocatorFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }
 
    public function execute()
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$objectManager->get('Magehit\Storelocator\Cron\MainTask')->execute();
        return;
    }
}