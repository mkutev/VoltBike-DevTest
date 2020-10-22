<?php
namespace Magehit\Storelocator\Controller\Index;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;

class Signuppost extends \Magento\Framework\App\Action\Action
{

    protected $_translitUrl;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filter\TranslitUrl $TranslitUrl
    ) {
        $this->_translitUrl = $TranslitUrl;
        parent::__construct($context);
    }

    
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        //var_dump($data);die;
        if ($data) {
            
            $model = $this->_objectManager->create('Magehit\Storelocator\Model\Storelocator');
            $data['store_name'] = $data['first_name'].' '.$data['last_name'];
            if(isset($data['lat']) || isset($data['lng'])){
               if ( $this->checkLatLng($data['lat'],$data['lng'])){
                    $this->messageManager->addErrorMessage(__('Error address already exists!')); 
                    $resultRedirect->setRefererOrBaseUrl();
                    return $resultRedirect;
                }
               
            }

            if(isset($data['products'])){
                $data['product_ids'] = trim($data['products'],',');
            }
            
            $data['store_url'] =  $this->getUrlcode($data['store_name']);
            $data['store_schedule'] = 0;
            $data['status'] = 0;
           // $data['in_store'] =  $this->_storeManager->getStore()->getId();
            
            $model->setData($data);
            
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Store.'));
                $resultRedirect->setUrl('/storelocator/index/index');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Store.'));
            }
        
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }
        $resultRedirect->setRefererOrBaseUrl();
        return $resultRedirect;
    }

    public function getUrlcode($name){
        $url = $this->_translitUrl->filter($name);
        $model =$this->_objectManager->create('Magehit\Storelocator\Model\Storelocator')->load($url,'store_url');
        $id =$model->getStorelocator_id();
        if(isset($id)){
            $url .= md5($id);
        }
        return $url;
    }
    public function checkLatLng($lat=null,$lng=null){
        $data =$this->_objectManager->create('Magehit\Storelocator\Model\Storelocator')->getCollection();
        if($data->getData()){
            foreach ($data->getData() as $item) {
                if($item['lat'] == $lat && $item['lng'] == $lng) return true;
            }
        }
        return false;
        
    }
}
