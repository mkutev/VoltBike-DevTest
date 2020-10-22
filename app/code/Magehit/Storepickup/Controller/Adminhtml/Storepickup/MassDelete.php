<?php
namespace Magehit\Storepickup\Controller\Adminhtml\Storepickup;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magehit\Storepickup\Model\ResourceModel\Storepickup\CollectionFactory ;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

class MassDelete extends \Magento\Backend\App\Action
{

    protected $filter;

    protected $collectionFactory;

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storepickup::manage');
    }
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $item) {
            $ruleId = $item->getRuleId();
            $item->delete();
            if($ruleId){
                $modelRule = $this->_objectManager->create('Magehit\Storepickup\Model\Rule');
                $modelRule->load($ruleId);
                $modelRule->delete();
            }
        }

        $this->messageManager->addSuccess(__('A total of %1 element(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}