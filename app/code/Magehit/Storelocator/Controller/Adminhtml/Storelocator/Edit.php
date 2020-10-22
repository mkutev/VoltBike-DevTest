<?php
namespace Magehit\Storelocator\Controller\Adminhtml\Storelocator;

class Edit extends \Magehit\Storelocator\Controller\Adminhtml\Storelocator
{

    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storelocator::storelocator_index_view');
    }
    public function execute()
    {

        $id = $this->getRequest()->getParam('storelocator_id');
        $model = $this->_objectManager->create('Magehit\Storelocator\Model\Storelocator');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Store no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('magehit_storelocator_storelocator', $model);
        $this->_coreRegistry->register('current_magehit_storelocator_items', $model->load($id));
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Store') : __('New Store'),
            $id ? __('Edit Store') : __('New Store')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Stores'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Store "%1"', $model->getStore_name()) : __('New Store'));
        return $resultPage;
    }
}
