<?php
namespace Magehit\Storelocator\Cron;

class MainTask {
	
	protected $_logger;
    protected $_objectManager;
    protected $_helper;
	
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magehit\Storelocator\Helper\Data $helper
    ) {
        $this->_logger 			= $logger;
		$this->_objectManager 	= $objectManager;
		$this->_helper 			= $helper;
    }

    /**
     * Method executed when cron runs in server
     */
    public function execute() {
		$this->_logger->critical('Start cron get data from instagram');
		
		ini_set('max_execution_time', '0');
		// instargram user id : 1167051402
		$instagramUserId = '1167051402';//
		$limit = 500;
        $profileUrl = 'https://www.instagram.com/graphql/query/?query_hash=472f257a40c653c64c666ce877d59d2b';
		$iterationUrl = $profileUrl . '&variables={"id":"'. $instagramUserId .'","first":50,"after":""}';
		$tryNext = true; 
		$found = 0;
		
		$images = array();
		while ($tryNext) {
			$tryNext = false;
			$response = file_get_contents($iterationUrl);
			if ($response === false) {
				break;
			}
			$data = json_decode($response, true);
			if ($data === null) {
				break;
			}
			$media = $data['data']['user']['edge_owner_to_timeline_media'];
			
			foreach ( $media['edges'] as $index => $node ) {
				array_push($images, $node);
			}
			
			$found += count($media['edges']);
			if ($media['page_info']['has_next_page'] && $found < $limit) {
				$iterationUrl = $profileUrl .'&variables={"id":"'. $instagramUserId .'","first":50,"after":"'. $media['page_info']['end_cursor'] .'"}';
				$tryNext = true;
			} 
		}
		/* $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Your text message'); */
		$access_token = '1167051402.56f48cc.749c2b47754b45f3b608311e313e8eeb';
		$pageInfoUri = 'https://api.instagram.com/v1/media/'; //B1IeEaUAy-B/?__a=1
		$storesLocations = array();
		$searchLocations = array();
		$i= 0;
		$dom = new \DOMDocument('1.0');
        $postNode = $dom->createElement("media");
        $parentnode = $dom->appendChild($postNode); 
		foreach($images as $_node){
			if($i >= $limit) break;
			$nodeData = $_node['node'];
			$pageInfoUriItemLink = $pageInfoUri . $nodeData['id'] . '?access_token=' . $access_token;
			//print_r($pageInfoUriItemLink);
			$responseItemData = @file_get_contents($pageInfoUriItemLink); 
			if(!$responseItemData){
				/* 
				$logger->info($pageInfoUriItemLink);
				$logger->info('---------------------'); */
				continue;
			}
			$data = json_decode($responseItemData, true);
			$postData = $data['data'];
			$location_id = $postData['location']['name'] != '' ? $postData['location']['id'] : "";
			$likeCount = array_key_exists("edge_media_preview_like",$nodeData)  ? $nodeData['edge_media_preview_like']['count'] : 0;
			$commentCount = array_key_exists("edge_media_to_parent_comment",$nodeData) ? $nodeData['edge_media_to_parent_comment']['count'] : 0;
			
			$postNode = $dom->createElement("post");
			$_postnode = $parentnode->appendChild($postNode);
			$_postnode->setAttribute("id", $nodeData['id']);
			$_postnode->setAttribute("shortcode",$nodeData['shortcode']);
			$_postnode->setAttribute("display_url", $nodeData['display_url']);
			$_postnode->setAttribute("thumbnail_src", $nodeData['thumbnail_src']);
			$_postnode->setAttribute("like_count", $likeCount);
			$_postnode->setAttribute("comment_count", $commentCount);
			$_postnode->setAttribute("created_at", $nodeData['taken_at_timestamp']);
			$_postnode->setAttribute("location", $location_id);
			$_postnode->setAttribute("location_name", $postData['location']['name']);
			$_postnode->setAttribute("title", htmlentities($nodeData['edge_media_to_caption']['edges'][0]['node']['text']));
			$_postnode->setAttribute("is_upload",0);
			
			if($postData['location']['name'] != ''){ 
				$searchLocations[$location_id] = array('name'=> $postData['location']['name'],'address_json'=> '');//$postData['location']['address_json']);
				
				$storesLocations[] = $location_id;
			}
			$i++;
		}
		
		$community_photos_upload = $this->_objectManager->get('Magehit\Storelocator\Model\PhotoFactory')->create()->getCollection()->addFieldToFilter('status', array('eq' => 1));
		foreach($community_photos_upload as $photo) {
			$locationName = $photo->getCity() . ', ' . $photo->getState();
			
			$locationId = @array_keys(array_combine(array_keys($searchLocations), array_column($searchLocations, 'name')), $locationName);
			$locationId = count($locationId) ? current($locationId) : $this->_helper->randomNumber(9);
			$postNode = $dom->createElement("post");
			$_postnode = $parentnode->appendChild($postNode);
			$_postnode->setAttribute("id", 'upload_photo_' . $photo->getPhotoId());
			$_postnode->setAttribute("shortcode","");
			$_postnode->setAttribute("display_url", $this->_helper->getUrlimage($photo->getImage()));
			
			$_postnode->setAttribute("thumbnail_src", $this->_helper->getUrlimage($photo->getThumbnaiImage()));
			$_postnode->setAttribute("like_count", 0);
			$_postnode->setAttribute("comment_count", 0);
			$_postnode->setAttribute("created_at", "");
			$_postnode->setAttribute("location", (string)$locationId);
			$_postnode->setAttribute("location_name", $locationName);
			$_postnode->setAttribute("title", htmlentities($photo->getDetails()));
			$_postnode->setAttribute("is_upload",1);
			$_postnode->setAttribute("name",$photo->getName());
			if($locationId){
				$searchLocations[$locationId] = array('name'=> $locationName,'address_json'=> json_encode(array('country_code'=>"")));
				$storesLocations[] = $locationId;
			}   
		}   
		
		
		$this->writeFile('instagram_media.xml',$dom->saveXML());
		
        $storesLocationsData =  array_count_values($storesLocations);
		
		
		
        $dom = new \DOMDocument('1.0');
        $node = $dom->createElement("markers");
        $parnode = $dom->appendChild($node);
        foreach ($storesLocationsData as $key=>$value) {
			$findLocationData = @$searchLocations[$key];
			$addressInfo = $this->getAddressInfo($findLocationData['name']);
            $node = $dom->createElement("marker");
            $newnode = $parnode->appendChild($node);
            $newnode->setAttribute("count", $value);
            $newnode->setAttribute("name", $findLocationData['name']);
			$address_json  = json_decode($findLocationData['address_json'], true);
			$countryCode  = $address_json['country_code'];
			
			$countryName  =  $countryCode ? @$this->_objectManager->get('Magento\Directory\Model\CountryFactory')->create()->loadByCode($countryCode)->getName() : '';
			
            $newnode->setAttribute("country", $countryName);
            $newnode->setAttribute("locationid", $key);
            $newnode->setAttribute("lat", $addressInfo['lat']);
            $newnode->setAttribute("lng", $addressInfo['lng']);
        }
		
		$this->writeFile('instagram_locations.xml',$dom->saveXML());
		
		$this->_logger->critical('End cron get data from instagram');
		
        return $this;
    }
	
	public function getAddressInfo($address) {
		$apiKey = $this->_helper->getConfig('map/api_key'); // Google maps now requires an API key.
		// Get JSON results from this request
		$geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&key='.$apiKey);
		$geo = json_decode($geo, true); 

		if (isset($geo['status']) && ($geo['status'] == 'OK')) {
			$latitude = $geo['results'][0]['geometry']['location']['lat']; // Latitude
			$longitude = $geo['results'][0]['geometry']['location']['lng']; // Longitude
		}
		return array('lat'=> $latitude, 'lng'=> $longitude);			
    }
	
	public function writeFile($fileName, $content) {
		$baseDir =  $this->_objectManager->get('Magento\Framework\Module\Dir\Reader')->getModuleDir('', 'Magehit_Storelocator');
        $myfile = fopen($baseDir . '/' . $fileName, "w") or die("Unable to open file!");
        try {
			fwrite($myfile, $content);
			fclose($myfile);
        } catch (Exception $e) {
			
        }
	}
}