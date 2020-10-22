<?php


namespace Magehit\Storepickup\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Storepickup extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    protected $_coreRegistry;
    protected $_code = 'storepickup';

    protected $_isFixed = true;

    protected $_rateResultFactory;

    protected $_rateMethodFactory;

    protected $_helperData;

    protected $_pickupFactory;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magehit\Storepickup\Helper\Data $dataHelper,
        \Magehit\Storepickup\Model\StorepickupFactory $storepickupFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helperData = $dataHelper;
        $this->_pickupFactory = $storepickupFactory;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		$checkoutSession = $objectManager->get('\Magento\Checkout\Model\Session');
		
        if (!$this->getConfigFlag('active') || $this->_helperData->getStorePickupAvaiable($checkoutSession->getQuote()) == Null) {
            return false;
        }
        $shippingPrice = $this->getConfigData('price');

        $result = $this->_rateResultFactory->create();

        if ($shippingPrice !== false) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));
			
			if(count($this->_helperData->getStorePickupAvaiable($checkoutSession->getQuote())) > 0){
				$shippingPrice = 0;
			}
            // custom set pickup store for quote address & request collect rates
			if($request->getPickupId()){
				$pickupId = $request->getPickupId();
				$pickupModel =  $this->_pickupFactory->create()->load($pickupId);
				$shippingPrice = $pickupModel->getHandlingFee();
				$methodName    = $pickupModel->getStoreName();
			}
            // if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
            //     $shippingPrice = '0.00';
            // }
            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
            if($this->_helperData->getStorePickupAvaiable($checkoutSession->getQuote()) != Null && !$this->_coreRegistry->registry('list_storepickup')){
                $listoption = array();
                $_pickupModel = $this->_pickupFactory->create();

                foreach ($this->_helperData->getStorePickupAvaiable($checkoutSession->getQuote()) as  $idItem) {
                    $listoption[]= [
                        'label'=>$_pickupModel->load($idItem)->getStoreName(),
                        'value'=>$idItem
                    ];
                }
                
                $this->_coreRegistry->register('list_storepickup', $listoption);
            }

            
           
            $result->append($method);
        }

        return $result;
    }
    public function getAllowedMethods()
    {
        return ['flatrate' => $this->getConfigData('name')];
    }
    public function getListPrd($request){
        $arr = $this->_helperData->getListProductIdsInRule();
        $prd = [];
        $rs = [];
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                $prd[]=$item->getProduct()->getId();
            }
        }
        foreach ($arr as $key =>  $value) {
            if(array_intersect($value,$prd) != Null){
                $rs[] = $key;
            }
        }
        return $rs;

    }
}
