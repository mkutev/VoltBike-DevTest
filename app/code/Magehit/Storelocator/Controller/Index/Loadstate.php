<?php
namespace Magehit\Storelocator\Controller\Index;
class Loadstate extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $_scopeConfig;
    protected $_helper;
    protected $_json;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magehit\Storelocator\Helper\Data $helper,
		\Magento\Framework\Controller\Result\JsonFactory $json
    )
    {
        $this->_scopeConfig 		  = $scopeConfig;
        $this->_resultPageFactory 	  = $resultPageFactory;
		$this->_helper 				  = $helper;
        $this->_json 	 			  = $json;
        parent::__construct($context);
    }
	
	public function execute()
    {
		$country = $this->getRequest()->getParam('country');
		$resultPage = $this->_resultPageFactory->create();
		$listState  = $this->_helper->getStateByCountry($country);
		$html = '';
		foreach($listState as $_state){
			$html .= '<option value="'. $_state['title'] .'">'. $_state['label'] .'</option>';
		}
		
		$dataResponse = array('html'=>$html);
		$resultJson = $this->_json->create();
		return $resultJson->setData($dataResponse);
    }
}