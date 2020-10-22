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

use \Magento\Framework\App\Helper;

class Cache extends Helper\AbstractHelper
{
    const CACHE_TAG = 'MAXIOLAB_YOUTUBER';
    const CACHE_ID = 'maxiolab_youtuber';
    protected $CACHE_LIFETIME = 3600;
    
    protected $cache;
    protected $cacheState;
    
    public function __construct(
        Helper\Context $context,
        \Magento\Framework\App\Cache $cache,
        \Magento\Framework\App\Cache\State $cacheState
    ) {
        $this->cache = $cache;
        $this->cacheState = $cacheState;
        parent::__construct($context);
        
        $this->CACHE_LIFETIME = $this->scopeConfig->getValue(
            'mxyoutuber_config/advanced/cache_lifetime',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getId($method, $vars = array())
    {
        return base64_encode(self::CACHE_ID . $method . implode('', $vars));
    }
    
    public function load($cacheId)
    {
        if ($this->cacheState->isEnabled(self::CACHE_ID)) { 
            return $this->cache->load($cacheId);
        }
        
        return false;
    }
    
    public function save($data, $cacheId)
    {
        if ($this->cacheState->isEnabled(self::CACHE_ID)) { 
            $this->cache->save($data, $cacheId, array(self::CACHE_TAG), $this->CACHE_LIFETIME);
            return true;
        }
        
        return false;
    }
    
    public function clean(){
        if ($this->cacheState->isEnabled(self::CACHE_ID)) { 
            $this->cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array(self::CACHE_TAG));
            return true;
        }
    }
    
}