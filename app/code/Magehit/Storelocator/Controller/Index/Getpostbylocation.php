<?php
 
namespace Magehit\Storelocator\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magehit\Storelocator\Model\StorelocatorFactory;
class Getpostbylocation extends \Magento\Framework\App\Action\Action
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
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		$locationId = $this->getRequest()->getParam('locationid');
		$page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 0; // default = 0
		$start = 0;
		$step = 5;
		$result = array();
		$next = $page *( $step );
		
		$mediaData 	= current($this->readXMl());
		if($locationId != 'all'){
			$arrMediaByLocation = array();
			foreach ($mediaData as $key => $item) {
				$itemData = $item['_attribute'];
				$arrMediaByLocation[$itemData['location']][] = $itemData;
			}
			//print_r($locationId);
			
			ksort($arrMediaByLocation, SORT_NUMERIC);
			$existMedia = array_key_exists($locationId,$arrMediaByLocation) ? $arrMediaByLocation[$locationId] : false; 
			
			$findMedia = array_slice($existMedia, $next, $step); 
			
			$result['media'] = $findMedia;
			$result['totalItem'] = count($existMedia);
			$result['load_more'] = count($existMedia) > ($next + $step) ? true : false;
			$result['location']  = $locationId;
		}else{
			$findMedia = array_slice($mediaData, $next, $step); 
			foreach($findMedia as $key => $item){
				$existMedia[] = $item['_attribute'];
			}
			$result['media'] = $existMedia;
			$result['current_page'] = $page;
			$result['totalItem'] = count($mediaData);
			$result['load_more'] = count($mediaData) > ($next + $step) ? true : false;
			$result['location']  = 'all';
		}
		
		$resultJson = $objectManager->get('Magento\Framework\Controller\Result\JsonFactory')->create();
		return $resultJson->setData($result); 
    }
	
	public function readXMl(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$filePath 		= $objectManager->get('Magento\Framework\Module\Dir\Reader')->getModuleDir('', 'Magehit_Storelocator').'/instagram_media.xml';
		$parsedArray 	= $objectManager->get('Magento\Framework\Xml\Parser')->load($filePath)->xmlToArray();
		return $parsedArray['media'];
	}
}