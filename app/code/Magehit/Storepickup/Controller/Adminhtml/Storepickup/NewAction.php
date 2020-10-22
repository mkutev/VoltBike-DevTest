<?php


namespace Magehit\Storepickup\Controller\Adminhtml\Storepickup;

class NewAction extends \Magehit\Storepickup\Controller\Adminhtml\Storepickup
{
    protected $_helperData;
    protected $resultForwardFactory;
    protected $ruleFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magehit\Storepickup\Model\RuleFactory $ruleFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magehit\Storepickup\Helper\Data $dataHelper
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_helperData = $dataHelper;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
