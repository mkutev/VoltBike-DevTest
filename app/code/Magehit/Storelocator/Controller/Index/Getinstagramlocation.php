<?php
 
namespace Magehit\Storelocator\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storelocator\Model\StorelocatorFactory;
class Getinstagramlocation extends \Magento\Framework\App\Action\Action
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
        $locationsData = $this->readXMl();
		
		$dom = new \DOMDocument('1.0');
        $node = $dom->createElement("markers");
        $parnode = $dom->appendChild($node);
		
        foreach ($locationsData as $location) {
			$store = $location['_attribute'];
			
            $node = $dom->createElement("marker");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("locationid", $store['locationid']);
			if($store['country'] != ''){
				if(strpos((string)$store['name'], (string)$store['country']) !== false){
					$nameHtml = $store['name'] ;
				}else{
					$nameHtml = $store['name'] . ', ' . $store['country'];
				}
			}else{
				$nameHtml = $store['name'];	
			}
            $newnode->setAttribute("name", $nameHtml);
            $newnode->setAttribute("count", $store['count']);
            $newnode->setAttribute("lat", $store['lat']);
            $newnode->setAttribute("lng", $store['lng']);
        }
        $result->setContents($dom->saveXML());
        return $result;
    }
	
	
	public function readXMl(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$filePath = $objectManager->get('Magento\Framework\Module\Dir\Reader')->getModuleDir('', 'Magehit_Storelocator').'/instagram_locations.xml';
		$parsedArray = $objectManager->get('Magento\Framework\Xml\Parser')->load($filePath)->xmlToArray();
		return $parsedArray['markers']['marker'];
	}
}