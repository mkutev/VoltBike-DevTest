<?php
/**
 * Maxiolab YoutubeR Extension
 *
 * @package     YoutubeR
 * @author		Maxiolab <lab.maxio@gmail.com>
 * @link		http://youtuber.maxiolab.com/?platform=magento
 * @copyright   Copyright (c) 2016. Maxiolab
 * @license     https://codecanyon.net/licenses/terms/regular
 */

namespace Maxiolab\Youtuber\Model\Config;

class Version extends \Magento\Framework\App\Config\Value implements \Magento\Framework\App\Config\Data\ProcessorInterface
{
    protected $moduleResource;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Module\ResourceInterface $moduleResource,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->moduleResource = $moduleResource;

        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function _getDefaultValue()
    {
        return (string)$this->moduleResource->getDbVersion('Maxiolab_Youtuber');
    }

    protected function _afterLoad()
    {
        $this->setValue($this->_getDefaultValue());

        return parent::_afterLoad();
    }

    public function processValue($value)
    {
        return $this->_getDefaultValue();
    }
}
