<?php


namespace Magehit\Storepickup\Controller\Adminhtml\Storepickup;

class Delete extends \Magehit\Storepickup\Controller\Adminhtml\Storepickup
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('storepickup_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Magehit\Storepickup\Model\Storepickup');
                $model->load($id);
                $ruleId = $model->getRuleId();
                $model->delete();
                if($ruleId){
                    $modelRule = $this->_objectManager->create('Magehit\Storepickup\Model\Rule');
                    $modelRule->load($ruleId);
                    $modelRule->delete();
                }
                
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Store.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['storepickup_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Store to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
