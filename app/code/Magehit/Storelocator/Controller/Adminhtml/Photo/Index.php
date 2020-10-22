<?php
namespace Magehit\Storelocator\Controller\Adminhtml\Photo;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storelocator::photo_index');
    }
    
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend(__("Photos"));
		return $resultPage;
    }
}
