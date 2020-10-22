<?php
namespace Magehit\Storelocator\Block\Adminhtml\Photo\Edit;

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
        $this->setId('magehit_storelocator_photo_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Photos'));
    }
}
