<?php

namespace Magehit\Customevent\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getScopeValue($path){
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getPresenterStatus(){
		return $this->getScopeValue('checkout/cart/enabled_presenter');
	}
	
	public function getListPresenter(){
		$arr = array(
			3 => __('Voltbike Ambassador'),
			1 => __('Google'),
			2 => __('Facebook'),
			4 => __('Other')
		);
		return $arr;
	}
}
