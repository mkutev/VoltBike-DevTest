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
        if (!$this->getConfigFlag('active') || $this->getListPrd($request) == Null) {
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
            if($request->getPickupId()){
                $pickupModel =  $this->_pickupFactory->create()->load($request->getPickupId());
                $shippingPrice = $pickupModel->getHandlingFee();
            }
            // if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
            //     $shippingPrice = '0.00';
            // }

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
            if($this->getListPrd($request) != Null && !$this->_coreRegistry->registry('list_storepickup')){
                $listoption = '';
                $_pickupModel = $this->_pickupFactory->create();

                foreach ($this->getListPrd($request) as  $idItem) {
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
