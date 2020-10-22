<?php
 
namespace Magehit\Storepickup\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storepickup\Model\StorepickupFactory;
class SaveQuote extends \Magento\Framework\App\Action\Action
{
    protected $_rawResultFactory;
    protected $_StorepickupFactory;
    protected $dataHelper;
    protected $_serialize;
	protected $_checkoutSession;

    public function __construct(
        Context $context,
        StorepickupFactory $StorepickupFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magehit\Storepickup\Helper\Data $dataHelper,
        \Magehit\Storepickup\Serialize\Serializer\Json $serialize
    )
    {
        $this->_rawResultFactory = $jsonResultFactory;
        $this->_StorepickupFactory = $StorepickupFactory;
        $this->dataHelper = $dataHelper;
        $this->_serialize = $serialize;
        parent::__construct($context);
    }
 
    public function execute()
    {
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		$cartModel = $objectManager->get('\Magento\Checkout\Model\Cart');
        $result = $this->_rawResultFactory->create();
        $params  = $this->getRequest()->getParams();
       
        $cartModel->getQuote()->setStorepickupData($this->_serialize->serialize(array(
			'id'   => @$params['store'],
			'date' => @$params['date'],
			'time' => @$params['time']
		)));
		$cartModel->getQuote()->save();
		$pickupData = $cartModel->getQuote()->getStorepickupData();
        $result->setData(array('status'=> true,'pickupData'=>$pickupData));
        return $result;
    }
}