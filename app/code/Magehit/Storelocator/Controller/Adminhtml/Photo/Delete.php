<?php
namespace Magehit\Storelocator\Controller\Adminhtml\Photo;

class Delete extends \Magehit\Storelocator\Controller\Adminhtml\Photo
{

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storelocator::photo_index_delete');
    }
	
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('photo_id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Magehit\Storelocator\Model\Photo');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Photo.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['photo_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Store to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
