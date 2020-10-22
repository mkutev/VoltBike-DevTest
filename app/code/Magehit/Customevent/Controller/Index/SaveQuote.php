<?php
 
namespace Magehit\Customevent\Controller\Index;
 
use Magento\Framework\App\Action\Context;
class SaveQuote extends \Magento\Framework\App\Action\Action
{
    protected $_jsonResultFactory;
    protected $_dataHelper;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magehit\Customevent\Helper\Data $dataHelper
    )
    {
        $this->_jsonResultFactory 	= $jsonResultFactory;
        $this->_dataHelper 			= $dataHelper;
        parent::__construct($context);
    }
 
    public function execute()
    {
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		$cartModel = $objectManager->get('\Magento\Checkout\Model\Cart');
        $result = $this->_jsonResultFactory->create();
        $params  = $this->getRequest()->getParam('custom');
		$presenter 	=	$params['presenter'];
		$ambassador = 	$params['ambassador'];
        $cartModel->getQuote()->setPresenter($presenter);
        $cartModel->getQuote()->setAmbassador($ambassador);
		$cartModel->getQuote()->save();
		$responseData = array(
			'presenter'		=> 	$cartModel->getQuote()->getPresenter(),
			'ambassador'	=>	$cartModel->getQuote()->getAmbassador()
		);
        $result->setData($responseData);
        return $result;
    }
}