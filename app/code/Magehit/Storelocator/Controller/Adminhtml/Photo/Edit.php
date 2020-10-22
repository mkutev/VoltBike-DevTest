<?php
namespace Magehit\Storelocator\Controller\Adminhtml\Photo;

class Edit extends \Magehit\Storelocator\Controller\Adminhtml\Photo
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
        return $this->_authorization->isAllowed('Magehit_Storelocator::photo_index_view');
    }
    public function execute()
    {

        $id = $this->getRequest()->getParam('photo_id');
        $model = $this->_objectManager->create('Magehit\Storelocator\Model\Photo');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This photo no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('magehit_storelocator_photo', $model);
        $this->_coreRegistry->register('current_magehit_photo_items', $model->load($id));
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Photo') : __('New Photo'),
            $id ? __('Edit Photo') : __('New Photo')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Photos'));
        $resultPage->getConfig()->getTitle()->prepend($model->getPhotoId() ? __('Edit Photo "%1"', $model->getName()) : __('New Photo'));
        return $resultPage;
    }
}
