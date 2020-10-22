<?php
namespace Magehit\Storelocator\Controller\Index;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;

class Uploadphoto extends \Magento\Framework\App\Action\Action
{

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
        \Magento\Framework\Filter\TranslitUrl $TranslitUrl,
		\Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
		\Magento\Framework\Image\AdapterFactory $adapterFactory,
		\Magento\Framework\Filesystem $filesystem,
		\Magehit\Storelocator\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_translitUrl = $TranslitUrl;
		$this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->_helper = $helper;
    }

    
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
			try {
            $model = $this->_objectManager->create('Magehit\Storelocator\Model\Photo');
            $data['status'] = 0;
			
			$uploaderFactory = $this->uploaderFactory->create(['fileId' => 'photo']);
            $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $imageAdapter = $this->adapterFactory->create();
            /* start of validated image */
            $uploaderFactory->addValidateCallback('community_photos_upload', $imageAdapter,'validateUploadFile');
            $uploaderFactory->setAllowRenameFiles(true);
            $uploaderFactory->setFilesDispersion(true);
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $destinationPath = $mediaDirectory->getAbsolutePath($this->_helper->getPhotoMediaFolder());
            $result = $uploaderFactory->save($destinationPath);
            if (!$result) {
                throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                );
            }
           
            $data['thumbnai_image'] = $this->_helper->resize($result['file'],480,480);
            $data['image'] = $this->_helper->getPhotoMediaFolder(). $result['file'];
			$countryName =  $this->_helper->loadCountryByCode($data['country']);
			$data['country'] = $countryName;
			
            $model->setData($data);
            
           
               $model->save();
                $this->messageManager->addSuccessMessage(__('You uploaded successful to the Store.'));
                $resultRedirect->setUrl('community-photos');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Sorry. File is too big. Max allowed size is 2MB'));
            }
        
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }
        $resultRedirect->setRefererOrBaseUrl();
        return $resultRedirect;
    }
}
