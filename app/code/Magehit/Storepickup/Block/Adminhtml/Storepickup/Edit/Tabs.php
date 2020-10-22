<?php
namespace Magehit\Storepickup\Block\Adminhtml\Storepickup\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magehit_storepickup_storepickup_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Store Pickup'));
    }
}
