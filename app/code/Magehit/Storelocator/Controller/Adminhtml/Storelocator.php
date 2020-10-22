<?php
namespace Magehit\Storelocator\Controller\Adminhtml;

abstract class Storelocator extends \Magento\Backend\App\Action
{

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storelocator::manager');
    }

    protected $_coreRegistry;
    const ADMIN_RESOURCE = 'Magehit_Storelocator::top_level';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }


    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Magehit'), __('Magehit'))
            ->addBreadcrumb(__('Storelocator'), __('Storelocator'));
        return $resultPage;
    }
}
