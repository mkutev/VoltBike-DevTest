<?php
 
namespace Magehit\Storepickup\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storepickup\Model\StorepickupFactory;
class Check extends \Magento\Framework\App\Action\Action
{
    protected $_rawResultFactory;
    protected $_StorepickupFactory;
    protected $dataHelper;
    protected $_serialize;
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
        $result = $this->_rawResultFactory->create();
        $kq=['success' => false];
        $data  = $this->getRequest()->getParams();
        

        if($data){
            $store = $this->_StorepickupFactory->create()->getCollection();
            if(isset($data['list']))
            //var_dump( explode(',',trim($data['list'],',')));
            $store->addFieldToFilter('storepickup_id', array('in' => explode(',',trim($data['list'],','))));
            if(isset($data['country_id']))
            $store->addFieldToFilter('country', array('eq' => $data['country_id']));
            if(isset($data['region_id']))
            $store->addFieldToFilter('region_id', array('eq' => $data['region_id']));
            // if(isset($data['region']))
            // $store->addFieldToFilter('region', array('eq' => $data['region']));
        
            if(count($store)){
                
                 foreach ($store as $_store) {
                     $kq['data'][] = json_encode(['label'=>$_store->getStoreName(),'value'=>$_store->getStorepickupId()]);
                 }
                 $kq['success'] = true;
            }
           
        }
        if(!isset($data['country_id']) || (!isset($data['region']) && !isset($data['region_id']) )){
            $kq['success'] = false;
        }
        $kq['params'] = $data;
        $result->setData($kq);
        return $result;
    }
}