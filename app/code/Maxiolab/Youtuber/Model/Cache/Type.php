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
namespace Maxiolab\Youtuber\Model\Cache;
 
class Type extends \Magento\Framework\Cache\Frontend\Decorator\TagScope
{
    const TYPE_IDENTIFIER = 'maxiolab_youtuber';
    const CACHE_TAG = 'MAXIOLAB_YOUTUBER';
 
    public function __construct(\Magento\Framework\App\Cache\Type\FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
 
}