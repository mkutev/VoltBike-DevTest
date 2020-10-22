<?php
namespace Magehit\Storelocator\Controller\Adminhtml\Photo;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    protected $_file;
    protected $dataPersistor;
    protected $_translitUrl;
	protected $uploaderFactory;
	protected $adapterFactory;
	protected $filesystem;
	protected $_helper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\Filter\TranslitUrl $TranslitUrl,
		\Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
		\Magento\Framework\Image\AdapterFactory $adapterFactory,
		\Magento\Framework\Filesystem $filesystem,
		\Magehit\Storelocator\Helper\Data $helper
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_file = $file;
        $this->_translitUrl = $TranslitUrl;
		$this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->_helper = $helper;
        parent::__construct($context);
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magehit_Storelocator::photo_index_save');
    }
    
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->get('Magehit\Storelocator\Helper\Data');
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('photo_id');
            $model = $this->_objectManager->create('Magehit\Storelocator\Model\Photo')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This photo no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
			
            if(isset($data['image'])){
                if(isset($data['image']['delete'])){
                    if(isset($data['image']['value'])) $this->deleteImage($data['image']['value']);
                    $data['image']='';

                }else{
                     unset($data['image']);
                }
            }
			if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
				try{
					$uploaderFactory = $this->uploaderFactory->create(['fileId' => 'image']);
					$uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
					$imageAdapter = $this->adapterFactory->create();
					$uploaderFactory->addValidateCallback('custom_image_upload',$imageAdapter,'validateUploadFile');
					$uploaderFactory->setAllowRenameFiles(true);
					$uploaderFactory->setFilesDispersion(true);
					$mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
					$destinationPath = $mediaDirectory->getAbsolutePath($helper->getPhotoMediaFolder());
					$result = $uploaderFactory->save($destinationPath);
					if (!$result) {
						throw new LocalizedException(
							__('File cannot be saved to path: $1', $destinationPath)
						);
					}
					
					$data['thumbnai_image'] = $helper->resize($result['file'],480,480);
					$data['image'] = $helper->getPhotoMediaFolder(). $result['file'];
					
				} catch (\Exception $e) {
				}
			   
				
			}
            
            $model->setData($data);
            
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Store.'));
                $this->dataPersistor->clear('magehit_storelocator_photo');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['photo_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Store.'));
            }
        
            $this->dataPersistor->set('magehit_storelocator_photo', $data);
            return $resultRedirect->setPath('*/*/edit', ['photo_id' => $this->getRequest()->getParam('photo_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    
    public function deleteImage($val){
        $url = $val;
        if ($this->_file->isExists($url)) {
            $this->_file->deleteFile($url);
        }
        return 0;
    }
   
}
