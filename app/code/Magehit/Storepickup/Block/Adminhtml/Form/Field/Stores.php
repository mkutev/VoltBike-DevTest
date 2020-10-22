<?php
namespace Magehit\Storepickup\Block\Adminhtml\Form\Field;
class Stores extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    protected function _prepareToRender()
    {
        $this->addColumn('store_name', ['label' => __('Store Name')]);
        $this->addColumn('street', ['label' => __('Street Address')]);
        $this->addColumn('phone', ['label' => __('Phone Number')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    // protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    // {
    //     $optionExtraAttr = [];
    //     $row->setData('option_extra_attrs',$optionExtraAttr);
    // }
    public function renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->_getCellInputElementName($columnName);

        if ($column['renderer']) {
            return $column['renderer']->setInputName(
                $inputName
            )->setInputId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setColumnName(
                $columnName
            )->setColumn(
                $column
            )->toHtml();
        }
        if($columnName !== 'phone'){
            return '<textarea id="' . $this->_getCellInputElementId(
            '<%- _id %>',
                $columnName
            ) .
                '"' .
                ' name="' .
                $inputName .
                '" value="<%- ' .
                $columnName .
                ' %>" ' .
                ($column['size'] ? 'size="' .
                $column['size'] .
                '"' : '') .
                ' class="' .
                (isset(
                $column['class']
            ) ? $column['class'] : 'textarea validate-no-empty') . '"' . (isset(
                $column['style']
            ) ? ' style="' . $column['style'] . '"' : '') . ' rows="2" ></textarea>';
        }else{
             return '<input type="text" id="' . $this->_getCellInputElementId(
            '<%- _id %>',
            $columnName
            ) .
                '"' .
                ' name="' .
                $inputName .
                '" value="<%- ' .
                $columnName .
                ' %>" ' .
                ($column['size'] ? 'size="' .
                $column['size'] .
                '"' : '') .
                ' class="' .
                (isset(
                $column['class']
            ) ? $column['class'] : 'input-text validate-no-empty') . '"' . (isset(
                $column['style']
            ) ? ' style="' . $column['style'] . '"' : '') . '/>';
        }
        
    }
}