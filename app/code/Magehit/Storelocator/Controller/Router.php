<?php
namespace Magehit\Storelocator\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use Magehit\Storelocator\Model\StorelocatorFactory;
use Magehit\Storelocator\Helper\Data;

class Router implements RouterInterface
{
    
    protected $actionFactory;
    protected $dataHelper;
    protected $_storeManager;
    protected $_request;
    protected $_model;

    public function __construct(
        ActionFactory $actionFactory,
         \Magento\Store\Model\StoreManagerInterface $storeManager,
         StorelocatorFactory $StorelocatorFactory,
        Data $helper
    )
    {
        $this->actionFactory = $actionFactory;
        $this->_storeManager =$storeManager;
        $this->_model = $StorelocatorFactory;
        $this->dataHelper = $helper;
    }
    public function match(RequestInterface $request)
    {
        if (!$this->dataHelper->getEnable()) {
            return null;
        }
       
        $route = $this->dataHelper->getIdentifier($this->_storeManager->getStore()->getId());
        $path = trim($request->getPathInfo(), '/');
        $controller = explode('/', $path);

        $identifier = str_replace($this->dataHelper->getUrlSuffix($this->_storeManager->getStore()->getId()), '', $path);
        
        if ( $identifier == $route ) {

            $request->setModuleName('storelocator')
                    ->setControllerName('index')
                    ->setActionName('index');
                   
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
                    
        }elseif($controller[0] == $route && count($controller) >= 2){

            $model = $this->_model->create()->load($controller[1],'store_url');
            $id =$model->getStorelocator_id();
            if(isset($id)){
                $request->setModuleName('storelocator')
                    ->setControllerName('index')
                    ->setActionName('detail')
                    ->setParams(['id'=>$id ]);
                   
                return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
            }
            return null;
        }
        return null;
    }
}
