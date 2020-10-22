<?php
 
namespace Magehit\Storelocator\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storelocator\Model\StorelocatorFactory; 
class Signup extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $dataHelper;
    protected $_storeManager;
    protected $_StorelocatorFactory;
    protected $_registry;
    public function __construct(
        Context $context, 
        StorelocatorFactory $StorelocatorFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magehit\Storelocator\Helper\Data $Helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_StorelocatorFactory = $StorelocatorFactory;
        $this->_storeManager = $storeManager;
        $this->dataHelper = $Helper;
        $this->_registry=$registry;
        parent::__construct($context);
    }
 
    public function execute()
    {
       
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Become VoltBike Ambassador'));
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}