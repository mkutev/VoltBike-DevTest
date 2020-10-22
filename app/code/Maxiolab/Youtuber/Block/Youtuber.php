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
namespace Maxiolab\Youtuber\Block;

class Youtuber extends \Magento\Framework\View\Element\Template {
	
	public $_errorMessage = '';
    protected $helper;
    public $video;
    public $playlist;
    public $channel;
    public $videos;
    public $playlists;
    public $featVideo;
    protected $appState;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Maxiolab\Youtuber\Helper\Data $helper,
		\Magento\Framework\App\State $appState,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->appState = $appState;
        $data = $this->helper->extendParams($this->helper->getDefaultAttribs(),$this->helper->cleanAttribs($data));
        parent::__construct($context, $data);
    }
	
    protected function _beforeToHtml(){
		if(!in_array($this->getData('wtype'),array('video','playlist','channel'))){
			$this->setData('wtype','video');
		}
		$this->setTemplate('Maxiolab_Youtuber::'.$this->getData('theme').'/'.$this->getData('wtype').'.phtml');
		
		$this->setData('limit',((int)$this->getData('cols')*(int)$this->getData('rows')));
	
		$method = 'prepare'.ucfirst($this->getData('wtype'));
		try{
			call_user_func(array($this,$method));
		}
		catch(\Exception $e){
			$this->_errorMessage = $e->getMessage();
		}

		return parent::_beforeToHtml();
	}
	
	public function prepareVideo(){
		$attribs = $this->getData();
		
		$this->video = $this->helper->getVideo( $this->getData('id') );
		$this->channel = $this->helper->getChannel( $this->video->snippet->channelId );

	}

	public function preparePlaylist(){
		$attribs = $this->getData();
		$dataModel = $this->helper;
		$this->playlist = new \stdClass;
		$ids = array();
		if(isset($attribs['videos'])&&trim($attribs['videos'])!=''){
			$tmp = explode(',',$attribs['videos']);
			foreach($tmp as $tm){
				$id = trim($tm);
				if($id!=''){
					$ids[] = $id;
				}
			}
		}
		else if($attribs['id']){
			$this->playlist = $dataModel->getPlaylistItems( $attribs['id'], $attribs['limit'], $attribs['pageToken']);
			foreach($this->playlist->items as $item){
				$ids[] = $item->snippet->resourceId->videoId;
			}
		}
		else{
			throw new \Exception('No playlist ID and no video IDs found.');
		}
        $this->videos = $dataModel->getVideo($ids);
		
		$attribs['rel'] = 'mxyoutuber_'.md5($attribs['id'].$attribs['theme']);
		
		$this->setData($attribs);
	}

	public function prepareChannel(){
		$attribs = $this->getData();
		$dataModel = $this->helper;
        $this->channel = $dataModel->getChannel( $attribs['id'] );	
		$playlists = array(
			$this->channel->contentDetails->relatedPlaylists->uploads => __('Uploads')
		);
		foreach($dataModel->getPlaylists($attribs['id']) as $dPlaylist){
			$playlists[$dPlaylist->id] = (isset($dPlaylist->snippet->title)?$dPlaylist->snippet->title:$dPlaylist->title);
		}
		$this->playlists = $playlists;

		if(!isset($attribs['playlist_id']) || !isset($this->playlists[$attribs['playlist_id']])){
			$attribs['playlist_id'] = $this->channel->contentDetails->relatedPlaylists->uploads;
		}
		
		$this->playlist = $dataModel->getPlaylistItems( $attribs['playlist_id'], $attribs['limit'], $attribs['pageToken'], false);
		$ids = array();
		if(isset($this->playlist->items)){
			foreach($this->playlist->items as $item){
				$ids[] = $item->snippet->resourceId->videoId;
			}
		}
		$this->videos = array();
		if(count($ids)){
			$this->videos = $dataModel->getVideo($ids);
		}
		$attribs['rel'] = 'mxyoutuber_'.strtolower($attribs['id']);

		
		$this->setData($attribs);
	}
    
    public function getVideoHref($video,$attribs,$iframe=false){
		$ytPlayerAttribs = array();
		$attribs['ytp_params'] = str_replace('&amp;','&',$attribs['ytp_params']);
        parse_str($attribs['ytp_params'],$ytPlayerAttribs);
        if($attribs['suggested_videos']=='false'){
			$ytPlayerAttribs['rel'] = '0';
		}
        switch($attribs['mode']){
            case 'embed':
                if(!isset($ytPlayerAttribs['autoplay'])) $ytPlayerAttribs['autoplay'] = 0;
            break;
            default:
                if(!isset($ytPlayerAttribs['autoplay'])) $ytPlayerAttribs['autoplay'] = 1;
            break;
        }
        if(!isset($ytPlayerAttribs['showinfo'])) $ytPlayerAttribs['showinfo'] = 1;

        if($iframe){
            return 'https://www.youtube.com/embed/'.$video->id.'?'.http_build_query($ytPlayerAttribs);
        }
        return 'https://youtu.be/'.$video->id.'?'.http_build_query($ytPlayerAttribs);
    }

	public function getVideoHTML($video,$attribs){
		$size = $attribs['size'];
		
		parse_str($attribs['ytp_params'],$ytPlayerAttribs);
		
		if($attribs['suggested_videos']=='false'){
			$ytPlayerAttribs['rel'] = '0';
		}

		switch($attribs['mode']){
			case 'embed':
				$html = '<iframe width="'.$attribs['width'].'" height="'.$attribs['height'].'" src="'.$this->getVideoHref($video,$attribs,true).'" frameborder="0" allowfullscreen></iframe>';
			break;
			case 'lightbox':
			case 'link':
			default:	
				$html = '<a href="'.$this->getVideoHref($video,$attribs).'" class="mxyt-videolink '.($attribs['mode']=='lightbox'?' mxyt-lightbox':'').'" '.((isset($attribs['rel'])&&$attribs['mode']=='lightbox')?'data-fancybox="'.$attribs['rel'].'"':'').' target="_blank">
					<span class="mxyt-play">
						<i class="mxyt-icon mxyt-icon-play"></i>
					</span>
					'.(isset($video->contentDetails->duration)?'<span class="mxyt-time">'.$this->getYouTubeTime($video->contentDetails->duration).'</span>':'').'
					<img src="'.$this->getThumbURL($video,$size).'" alt="'.htmlentities($video->snippet->title).'" />
				</a>';
			break;
		}
		return $html;
	}
	
	public function getYouTubeTime($str){
		$int = new \DateInterval($str);
	
		if($int->h != 0){
			$duration = $int->format('%h:%I:%S');
		}
		else{
			$duration = $int->format('%i:%S');
		}
	
		return $duration;
	}
	
	public function getThumbURL($video,$size){
		return (isset($video->snippet->thumbnails->$size)?$video->snippet->thumbnails->{$size}->url:$video->snippet->thumbnails->default->url);
	}
	
	public function getLimitVideoDescr($text,$num_words){
		$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
		$sep = ' ';
		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
		}
		$text = implode( $sep, $words_array );
		$text = preg_replace_callback('~([^\s]{24})([^\s])~is',create_function('$match','return $match[1]." ".$match[2];'),$text);
		return $text;
	}

	public function getFullVideoDescr($str){
		$str = preg_replace_callback('~(https?://[^\s]+)~i',create_function('$matches','$title = (strlen($matches[1])>25?substr($matches[1],0,25)."...":$matches[1]);return "<a href=\"{$matches[1]}\" target=\"_blank\" rel=\"nofollow\">{$title}</a>";'),$str);
		return $str;
	}
    
    public function canDisplay($blockName){
        return strpos($this->getData('display'),$blockName)!==false;
    }
	
    public function getAjaxCfg($override=null){
        if(!is_array($override)){
            $override = array();
        }
        $cfg = $this->helper->extendParams($this->helper->cleanAttribs($this->getData()),$override);
        if(isset($cfg['pageToken'])) unset($cfg['pageToken']);
        if(isset($cfg['type'])) unset($cfg['type']);
        if(isset($cfg['infinite_scroll'])) unset($cfg['infinite_scroll']);
        return http_build_query($cfg);
    }
	
	public function getTemplateFile($template = NULL){
		$file = parent::getTemplateFile($template);
		if($file===false){
            $this->setTemplate('Maxiolab_Youtuber::default/'.$this->getData('wtype').'.phtml');
		}
		return parent::getTemplateFile($template);
	}
	
	public function _toHtml(){
		if($this->_errorMessage!=''){
            $this->helper->logError($this->_errorMessage);
			if($this->appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER){
				return '<div class="alert alert-danger" role="alert"><strong>YouTubeR error:</strong> '.$this->_errorMessage.'</div>';
			}
			return 'YouTubeR error.';
		}
		else{
			return parent::_toHtml();
		}
	}
	
}
