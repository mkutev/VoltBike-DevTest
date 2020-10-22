<?php
 
namespace Magehit\Storelocator\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storelocator\Model\StorelocatorFactory;
class Search extends \Magento\Framework\App\Action\Action
{
    protected $_rawResultFactory;
    protected $_StorelocatorFactory;
    protected $dataHelper;
    public function __construct(
        Context $context,
        StorelocatorFactory $StorelocatorFactory,
        \Magento\Framework\Controller\Result\RawFactory $rawResultFactory,
        \Magehit\Storelocator\Helper\Data $dataHelper
    )
    {
        $this->_rawResultFactory = $rawResultFactory;
        $this->_StorelocatorFactory = $StorelocatorFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }
 
    public function execute()
    {
        $result = $this->_rawResultFactory->create();
        $result->setHeader('Content-Type', 'text/xml');
        $collection  =  $this->_StorelocatorFactory->create();
        if($this->getRequest()->getParam('all')==1){
            $stores = $collection->getCollection()->locationActive()->load(); 
        }else{
            $stores = $collection->getCollection()->locationFilters()->load();
        }
        
        $dom = new \DOMDocument('1.0');
        $node = $dom->createElement("markers");
        $parnode = $dom->appendChild($node);
        foreach ($stores as $store) {
            $node = $dom->createElement("marker");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("id", $store['storelocator_id']);
            if(trim($store['store_thumnail']) !=''){
                $newnode->setAttribute("thumnail", $this->dataHelper->getUrlimage($store['store_thumnail']));
            }else{
                $newnode->setAttribute("thumnail", $this->dataHelper->getDefaultimage());
            }
            $newnode->setAttribute("name", $store['store_name']);
            $newnode->setAttribute("address", "Street hidden" . '<br>' . $store['city']. ', ' . $store['region'] . ' ' . $store['postcode'] . '<br>' . $store['country']);
            $newnode->setAttribute("lat", $store['lat']);
            $newnode->setAttribute("lng", $store['lng']);
            $number = "";
            if($store['telephone']){
                $number = $this->phone_number_format($store['telephone']);
            }
            $newnode->setAttribute("phone", $number);
            $newnode->setAttribute("url", $this->dataHelper->getUrlstore($store['store_url']));
            $newnode->setAttribute("distance", $store['distance']);
        }
        $result->setContents($dom->saveXML());
        return $result;
    }
    public function phone_number_format($number) {
                  // Allow only Digits, remove all other characters.
                  $number2 = preg_replace("/[^\d]/","",$number);
                 
                  // get number length.
                  $length = strlen($number2);
                 
                 // if number = 10
                 if($length == 10) {
                  $number2 = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $number2);
                  return $number2;
                 }
                  
                  return $number;
                 
    }
}