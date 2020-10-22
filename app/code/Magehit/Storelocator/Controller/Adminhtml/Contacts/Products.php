<?php
namespace Magehit\Storelocator\Controller\Adminhtml\Contacts;
use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
class Products extends \Magento\Backend\App\Action
{

    protected $_resultLayoutFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    protected function _isAllowed()
    {
        return true;
    }

    public function execute()
    {
        
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('store.edit.tab.products')
                     ->setInProducts($this->getRequest()->getPost('storelocator_id', null));
        return $resultLayout;
    }
}