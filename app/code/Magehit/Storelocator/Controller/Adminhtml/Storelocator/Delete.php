<?php
namespace Magehit\Storelocator\Controller\Adminhtml\Storelocator;

class Delete extends \Magehit\Storelocator\Controller\Adminhtml\Storelocator
{

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storelocator::storelocator_index_delete');
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('storelocator_id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Magehit\Storelocator\Model\Storelocator');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Store.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Store to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
