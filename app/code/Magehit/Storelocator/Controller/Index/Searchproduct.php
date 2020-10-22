<?php
 
namespace Magehit\Storelocator\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storelocator\Model\StorelocatorFactory;
class Searchproduct extends \Magento\Framework\App\Action\Action
{
    protected $_rawResultFactory;
    protected $_StorelocatorFactory;
    protected $_productCollection;
    protected $dataHelper;
    public function __construct(
        Context $context,
        StorelocatorFactory $StorelocatorFactory,
        \Magento\Framework\Controller\Result\RawFactory $rawResultFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magehit\Storelocator\Helper\Data $dataHelper
    )
    {
        $this->_rawResultFactory = $rawResultFactory;
        $this->_StorelocatorFactory = $StorelocatorFactory;
        $this->_productCollection = $productCollectionFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }
 
    public function execute()
    {
        $result = $this->_rawResultFactory->create();
        $result->setHeader('Content-Type', 'text/xml');
        $productCollection = $this->_productCollection->create();
        
        

        $productId = "";
        // Get parameters from URL
        $search_text = $this->getRequest()->getParam('search_text');
        $search_type = $this->getRequest()->getParam('search_type');
        
        // Get productId by some the case
        if($search_type == "sku"){
            $product_collection = $productCollection
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('SKU', array('like' => '%'.$search_text.'%'))
            ->load();
            foreach($product_collection as $prod) {
                $productId = $prod->getId();
            }    
        }
        elseif($search_type == "name"){
            $product_collection = $productCollection
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('name', array('like' => '%'.$search_text.'%'))
            ->load();
            foreach($product_collection as $prod) {
                $productId = $prod->getId();
            }    
        }
        else{
            $productId = $search_text;    
        }
        $collection  =  $this->_StorelocatorFactory->create();
        $stores = $collection->getCollection()->productFilters(trim($productId))->load(); 
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
            $newnode->setAttribute("address", $store['street'] . '<br>' . $store['city']. ', ' . $store['region'] . ' ' . $store['postcode'] . '<br>' . $store['country']);
            $newnode->setAttribute("lat", $store['lat']);
            $newnode->setAttribute("lng", $store['lng']);
            $newnode->setAttribute("url", $this->dataHelper->getUrlstore($store['store_url']));
            $newnode->setAttribute("phone", $store['telephone']);
            $newnode->setAttribute("distance", $store['distance']);
        }
        $result->setContents($dom->saveXML());
        return $result;
    }
}