<?php
namespace Magehit\Storepickup\Model\ResourceModel;
 
class Rule extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    protected function _construct()
    {
        $this->_init('magehit_storepickup_rules', 'rule_id');
    }
    
}