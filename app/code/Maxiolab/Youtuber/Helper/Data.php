<?php
/**
 * Maxiolab YoutubeR Extension
 *
 * @package     YoutubeR
 * @author		Maxiolab <lab.maxio@gmail.com>
 * @link		http://youtuber.maxiolab.com/?platform=magento
 * @copyright   Copyright (c) 2016. Maxiolab
 * @license     https://codecanyon.net/licenses/terms/regular
 */
namespace Maxiolab\Youtuber\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    
    protected $storeManager;
    protected $logger;
    protected $cache;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Maxiolab\Youtuber\Helper\Cache $cache
    ) {
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->cache = $cache;
        parent::__construct($context);
    }
	
	public function getConfigFlag($var){
        return $this->scopeConfig->getValue(
            'mxyoutuber_config/'.$var,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ? true : false;
		//return Mage::getStoreConfigFlag('mxyoutuber_config/'.$var);
	}

	public function getConfigVar($var){
		return (string)$this->scopeConfig->getValue(
            'mxyoutuber_config/'.$var,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	
	public function cleanAttribs($attribs){
		$clean = array();
		$defaults = array_keys($this->getDefaultAttribs());
		foreach($attribs as $k=>$v){
			if(in_array($k,$defaults)){
				$clean[$k] = $v;
			}
		}
		return $clean;
	}
	
	public function getDefaultAttribs(){
		return array(
			'wtype' => 'video',
			'id' => '',
			'videos' => '',
			'display' => 'title,date,channel,description,meta',
			'mode' => 'lightbox',
			'theme' => 'default',
			'ytp_params' => '',
			'size' => 'medium',
			'width' => '100%',
			'height' => '300',
			'cols' => 2,
			'rows' => 3,
			'limit' => 6,
			'date_format' => 'd.m.Y',
			'responsive_limit' => 'sm',
			'max_words' => 20,
			'infinite_scroll' => 0,
			'load_more' => 0,
			'load_more_text' => __('Load more'),
			'pageToken' => '',
			'suggested_videos' => 0,
			'playlist_id' => '',
		);
	}

	public function getVideo($id){
		$response = $this->loadData( 'videos' , array(
            'part'          => 'snippet,statistics,contentDetails',
            'maxResults'    => (is_array($id)?count($id):1),
            'id'            => (is_array($id)?implode(',',$id):$id)
        ));

        if( !isset($response->items[0]) ) {
            if(is_array($id)){
				throw new \Exception('Videos IDs: '.implode(' ,',$id).' not found');
			}
			else{
				throw new \Exception('Video ID: '.$id.' not found');
			}
        }
        
        return (is_array($id)?$response->items:$response->items[0]);
	}

	public function getChannel($id){
        $response = $this->loadData( 'channels' , array(
            'part' => 'snippet,contentDetails,brandingSettings,statistics',
            'id' => $id
        ));
        if( !isset($response->items[0]) ) {
            throw new \Exception('Channel ID: '.$id.' not found');
        }

        return $response->items[0];
	}
	
	public function getPlaylistItems($id,$limit=20,$pageToken='',$strict=true){
        $params = array(
            'part' => 'snippet',
            'maxResults' => $limit,
            'playlistId' => $id
        );
        if($pageToken != '') {
            $params['pageToken'] = $pageToken;
        }
		
        $response = $this->loadData( 'playlistItems' , $params);

        if( $strict && !isset($response->items[0]) ) {
            throw new \Exception('Playlist ID:'.$id.' not found');
        }

        return $response;
	}
	
	public function getPlaylists($channelID){
        $params = array(
            'part' => 'snippet',
            'channelId' => $channelID,
			'maxResults' => 50
        );
        $response = $this->loadData( 'playlists' , $params);
        if( !isset($response->items[0]) ) {
            throw new \Exception('Playlists for channel ID:'.$channelID.' not found');
        }

        return $response->items;
	}
	
    private function getRequestURI( $type,$data ){
        $data['key'] = $this->getConfigVar('general/googleBrowserKey');
        return 'https://www.googleapis.com/youtube/v3/'.$type.'?'.http_build_query( $data );
    }

	private function loadData($type,$data){
		$qID = 'mxyoutuber_'.md5($this->getRequestURI( $type,$data ));
		$cached = $this->cache->load($qID);
		if($cached!==false){
			return json_decode(base64_decode($cached));
		}

		$result = $this->doLoadData($this->getRequestURI($type,$data));
		
		if(strpos($result,'{')!==0){
			throw new \Exception('YouTube server responce error.<pre>'.print_r($result,true).'</pre>');
		}
		$body = json_decode($result);
		if(isset($body->error)) {
			throw new \Exception('YouTube server responce error.<pre>'.print_r($result,true).'</pre>');
		}
		
		$this->cache->save(base64_encode($result), $qID);

		return $body;
	}
	
	private function doLoadData($url, $params = array(), $post=0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		if($post){
			curl_setopt($ch, CURLOPT_POST, true);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'User-Agent' => 'Mozilla/5.0 (Windows NT 5.1; rv:15.0) Gecko/20100101 Firefox/15.0.1',
			'Accept' => 'application/json',
			'Referer' => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK),
		));  
		$contents = curl_exec($ch);
		curl_close($ch);
		return $contents;
	}
    
    public function extendParams($arr1,$arr2){
        return array_merge($arr1,$arr2);
    }
	
    public function logError($msg){
		if(is_callable(array($this->logger,'addError'))){
			$this->logger->addError($msg);
		}
		if(is_callable(array($this->logger,'error'))){
			$this->logger->error($msg);
		}
    }
}
