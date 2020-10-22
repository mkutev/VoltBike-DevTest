<?php
namespace Magehit\Storelocator\Controller\Adminhtml\Storelocator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    protected $_file;
    protected $dataPersistor;
    protected $_translitUrl;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\Filter\TranslitUrl $TranslitUrl
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_file = $file;
        $this->_translitUrl = $TranslitUrl;
        parent::__construct($context);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storelocator::storelocator_index_save');
    }
    
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        // var_dump($data);die;
        if ($data) {
            //var_dump($data);die;
            $id = $this->getRequest()->getParam('storelocator_id');
            $model = $this->_objectManager->create('Magehit\Storelocator\Model\Storelocator')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Storelocator no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if(isset($data['lat']) || isset($data['lng'])){
               if ( $this->checkLatLng($data['lat'],$data['lng']) && !isset($id) ){
                    $this->messageManager->addErrorMessage(__('Error address already exists!'));
                    
                    return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $this->getRequest()->getParam('storelocator_id')]);
                }
                // $location = $this->checkMap($data);
                // if($location['status']){
                //     $data['lat'] = $location['lat'];
                //     $data['lng'] =$location['lng'];
                // }else{
                //     $this->messageManager->addErrorMessage(__('"'.$location['address'].'" - '.'Address not found.'));
                //     return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $this->getRequest()->getParam('storelocator_id')]);
                // }
            }

            if(isset($data['products'])){
                $data['product_ids'] = str_replace( '&', ',',  trim($data['products']));
            }
            if(!isset($data['store_thumnail'])){
                $_img = $this->getimage();
                if($_img['status'] == 1){
                    $data['store_thumnail'] = $_img['url'];
                }elseif($_img['status'] == -1){
                    if(isset($_img['message']))
                    $this->messageManager->addErrorMessage($_img['message']);
                    return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $this->getRequest()->getParam('storelocator_id')]);
                }
            }
            else{
                if(isset($data['store_thumnail']['delete'])){
                    if(isset($data['store_thumnail']['value'])) $this->deleteImage($data['store_thumnail']['value']);
                    $data['store_thumnail']='';

                }else{
                     unset($data['store_thumnail']);
                }
            }
            if(isset($data['schedule'])){
                if(is_array($data['schedule'])){
                    $data['schedule'] = serialize ($data['schedule']);
                }else{
                    $this->messageManager->addErrorMessage( __('Something went wrong while saving the Storelocator.'));
                    return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $this->getRequest()->getParam('storelocator_id')]);
                }
            }
            if(!(trim($data['store_url']))){
                   $data['store_url'] =  $this->getUrlcode($data['store_name']);
            }
            if($data['in_store']){
                $data['in_store'] = (implode(",",$data['in_store']));
            }
            $model->setData($data);
            
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Store.'));
                $this->dataPersistor->clear('magehit_storelocator_storelocator');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Store.'));
            }
        
            $this->dataPersistor->set('magehit_storelocator_storelocator', $data);
            return $resultRedirect->setPath('*/*/edit', ['storelocator_id' => $this->getRequest()->getParam('storelocator_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function checkMap($data){
        $address = "";
        $result['status']=0;
        try{
            if(trim($data['street'])) $address.= trim($data['street']);
            if(trim($data['city'])) $address.= ','.trim($data['city']);
            if(trim($data['region'])) $address.= ','.trim($data['region']);
            if(trim($data['country'])) $address.= ','.trim($data['country']);
            $url = "https://maps.google.com/maps/api/geocode/json?address=".urlencode($address);
            $result['address'] =$address;

            $ch = curl_init();
            $timeout = 10; // set to zero for no timeout
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $data = curl_exec($ch);
            curl_close($ch);
            //$data = file_get_contents($url);
            $jsondata = json_decode($data);
            if( (string)($jsondata->{'status'}) == 'OK'  ){
               $result['status']=1;
               $result['lat'] = (float)$jsondata->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                $result['lng'] = (float)$jsondata->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            }
        }
        catch (Exception $e){

        }
        return $result;
    }
    public function deleteImage($val){
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
        $url = $mediaDirectory->getAbsolutePath().'/'.$val;
        if ($this->_file->isExists($url)) {
            $this->_file->deleteFile($url);
        }
        return 0;
    }
    public function getimage(){
        $results['status']=0;
        $results['message'] = __('Null');
        $profileImage = $this->getRequest()->getFiles('store_thumnail');
        $fileName = ($profileImage && array_key_exists('name', $profileImage)) ? $profileImage['name'] : null;
        if ($profileImage && $fileName) {
            $results['status']=-1;
            $results['message'] = __('Save imgaes error.');
            try {
                
                $uploader = $this->_objectManager->create(
                    'Magento\MediaStorage\Model\File\Uploader',
                    ['fileId' => 'store_thumnail']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                
                $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                    ->create();
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
               
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);

                $result = $uploader->save($mediaDirectory->getAbsolutePath('storelocator/images'));
                $results['status'] = 1;
                $results['url'] = 'storelocator/images/'. $result['file'];
               
            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $results['message'] = $e->getMessage();
                }
            }
        }
        return $results;
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
