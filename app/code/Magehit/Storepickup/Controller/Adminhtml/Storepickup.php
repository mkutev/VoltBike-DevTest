<?php


namespace Magehit\Storepickup\Controller\Adminhtml;

abstract class Storepickup extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Magehit_Storepickup::top_level';
    protected $_coreRegistry;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storepickup::manage');
    }
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Magehit'), __('Magehit'))
            ->addBreadcrumb(__('Storepickup'), __('Store'));
        return $resultPage;
    }
}
