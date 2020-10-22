<?php
 
namespace Magehit\Storelocator\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storelocator\Model\StorelocatorFactory;
class Index extends \Magento\Framework\App\Action\Action
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
        if($this->getRequest()->getParam('id')){
            $this->_forward('Detail','Index');
        }
        $collection  =  $this->_StorelocatorFactory->create();
        $stores = $collection->getCollection()->addFieldToFilter('status', array('eq' => 1));
        $this->_registry->register('storelocator_model', $stores);

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set($this->dataHelper->getConfig('general/page_title',$this->_storeManager->getStore()->getId()));
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}