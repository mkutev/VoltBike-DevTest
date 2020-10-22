<?php
namespace Magehit\Storepickup\Model\Config\Source\Backend;

class Stores extends \Magento\Framework\App\Config\Value
{
    protected $_customtabs = null;
    protected $_serialize;
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magehit\Storepickup\Serialize\Serializer\Json $serialize
    ) {
        $this->_serialize = $serialize;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        
    }
    

    protected function _afterLoad()
    {
        $value = $this->getValue();
        $arr = $this->_serialize->unserialize($value);

        $this->setValue($arr);
    }

    public function beforeSave()
    {
        $value = $this->getValue();
        unset($value['__empty']);
        $arr = $this->_serialize->serialize($value);
        
        $this->setValue($arr);
    }
}