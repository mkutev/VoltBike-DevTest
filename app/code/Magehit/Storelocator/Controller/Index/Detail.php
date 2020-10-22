<?php
 
namespace Magehit\Storelocator\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storelocator\Model\StorelocatorFactory; 
class Detail extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $_StorelocatorFactory;
    protected $_messageManager;
    protected $_registry;
    public function __construct(
        Context $context,
        StorelocatorFactory $StorelocatorFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_StorelocatorFactory = $StorelocatorFactory;
        $this->_messageManager = $messageManager;
        $this->_registry=$registry;
        parent::__construct($context);
    }
 
    public function execute()
    {
        
        $storelocatorId     = $this->getRequest()->getParam('id');
        
        $storelocatorModel  = $this->_StorelocatorFactory->create()->load($storelocatorId);
       
        if ($storelocatorModel->getStorelocator_id()) {
               $this->_registry->register('storelocator_data', $storelocatorModel);
               $this->_view->getPage()->getConfig()->getTitle()->set($storelocatorModel->getStore_name());
               // $this->_view->loadLayout();
               // $this->_view->renderLayout();

        } else {
            $this->_messageManager->addError(__('Store does not exist'));
            $this->_redirect('*/*/');
        }
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}