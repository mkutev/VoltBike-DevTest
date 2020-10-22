<?php
namespace Magehit\Storelocator\Block\Adminhtml\Photo;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $_coreRegistry = null;
    protected $dataHelper;
    //protected $_storeManager;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magehit\Storelocator\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->dataHelper = $dataHelper;
        //$this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }


    protected function _construct()
    {
        $this->_objectId = 'photo_id';
        $this->_controller = 'adminhtml_photo';
        $this->_blockGroup = 'Magehit_Storelocator';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );
    }


    public function getHeaderText()
    {
        $item = $this->_coreRegistry->registry('current_magehit_photo_items');
        if ($item->getId()) {
            return __("Edit Item '%1'", $this->escapeHtml($item->getStore_name()));
        } else {
            return __('New Item');
        }
    }
}
