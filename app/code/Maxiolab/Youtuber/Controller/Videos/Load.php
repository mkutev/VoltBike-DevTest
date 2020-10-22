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

namespace Maxiolab\Youtuber\Controller\Videos;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Load extends Action
{
    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $helper;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Maxiolab\Youtuber\Helper\Data $helper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute(){
        $result = array(
            'success' => 0,
        );
        if($this->getRequest()->isAjax()){
            //$this->getRequest()->getParam('data')
            parse_str(filter_var($this->getRequest()->getParam('params',''),FILTER_SANITIZE_STRING),$attribs);
			$attribs = $this->helper->cleanAttribs($attribs);
			$attribs['pageToken'] = filter_var($this->getRequest()->getParam('pageToken', ''),FILTER_SANITIZE_STRING);

			$view = $this->resultPageFactory->create()->getLayout()->createBlock('Maxiolab\Youtuber\Block\Youtuber','mxyoutuber',$attribs);
            $view->addData($attribs);
            
			$result['success'] = 1;
			$result['html'] = $view->toHtml();
			if($view->_errorMessage!=''){
				$result['success'] = 0;
				$result['html'] = '';
			}
			else{
                $result['pageToken'] = (isset($view->playlist->nextPageToken)?$view->playlist->nextPageToken:'');
			}
        }
        return $this->resultJsonFactory->create()->setData($result);
    }
}
