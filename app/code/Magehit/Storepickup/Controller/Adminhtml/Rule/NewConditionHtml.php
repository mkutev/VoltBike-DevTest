<?php
namespace Magehit\Storepickup\Controller\Adminhtml\Rule;

class NewConditionHtml extends \Magento\Backend\App\Action
{

   
    protected $_coreRegistry;
    protected $fileFactory;
    protected $dateFilter;
    protected $_storepickupFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
         \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magehit\Storepickup\Model\RuleFactory $storepickupFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->fileFactory = $fileFactory;
        $this->dateFilter = $dateFilter;
        $this->_storepickupFactory = $storepickupFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storepickup::manage');
    }
    public function execute()
    {
        
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->_objectManager->create(
            $type
        )->setId(
            $id
        )->setType(
            $type
        )->setRule(
            $this->_objectManager->create(\Magento\CatalogRule\Model\Rule::class)
        )->setPrefix(
            'conditions'
        );
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}