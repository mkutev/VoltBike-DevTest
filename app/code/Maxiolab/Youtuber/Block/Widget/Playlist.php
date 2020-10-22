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

namespace Maxiolab\Youtuber\Block\Widget;

class Playlist extends \Maxiolab\Youtuber\Block\Youtuber implements \Magento\Widget\Block\BlockInterface
{

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Maxiolab\Youtuber\Helper\Data $helper,
		\Magento\Framework\App\State $appState,
        array $data = []
    ) {
        $this->helper = $helper;
        
        $data = $this->helper->extendParams($this->helper->getDefaultAttribs(),$this->helper->cleanAttribs($data));
        
        $data['wtype'] = 'playlist';
        parent::__construct($context, $helper, $appState, $data);
    }

}
