<?php
namespace David\Customevent\Controller\Support;
use Magento\Framework\App\Filesystem\DirectoryList;
class Send extends \Magento\Framework\App\Action\Action
{
    protected $coreRegistry = null;
    protected $fileFactory;
    protected $dateFilter;
    protected $logger;
    protected $resultPageFactory;
    // private $secret_key = '6Lcqk9UUAAAAAFSK6EEunpW5_LYB-ijr589kFAxx';
    private $fileUploaderFactory;
    private $fileSystem;
    private $helper;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        parent::__construct($context);
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->fileSystem          = $fileSystem;
		$this->coreRegistry 		= $coreRegistry;
        $this->fileFactory 			= $fileFactory;
        $this->dateFilter 			= $dateFilter;
        $this->logger 				= $logger;
        $this->resultPageFactory 	= $resultPageFactory;
    }
 
    /**
     * Rule save action
     *
     * @return void
     */
    

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $helper = $objectManager->create('David\Customevent\Helper\Data');
        $requestData = $this->getRequest()->getParams();
            $subject = $requestData['subject'];
            $message = $requestData['description'];
            $classi = $requestData['classifications'];

            $token = '';

            $filesystem = $objectManager->get('Magento\Framework\Filesystem');
            $directoryList = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
            $media = $filesystem->getDirectoryWrite($directoryList::MEDIA);
           
            $checkToken = explode("\n", $media->readFile('token/value.txt'));
            if(isset($checkToken[1]) && trim($checkToken[1]) > time()){
                $token = trim($checkToken[0]);
            }else{
                $token = $helper->getToken();
                $media->writeFile("token/value.txt",$token."\n");
                $media->writeFile("token/value.txt",time()+3600);
            }

            $orgId = $helper->getOrgId($token);

            $customer = $helper->createCustomer($requestData['contact_name'], $token, $orgId);

            $customField = [
                'cf_which_volt_bike_product' => $requestData['product'],
                'cf_order_date' => $requestData['order_date'],
                'cf_order_number' => $requestData['order_number']
            ];
            $file = $this->getRequest()->getFiles('attachment');
            $fileUrl = '';
            if(!empty(trim($file['name']))){
                try {
                    $uploader = $this->fileUploaderFactory->create(['fileId' => 'attachment']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'zip', 'mp4', 'avi', 'wmv']);
                    $path = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('support');
                    $result = $uploader->save($path);
                    $upload_document = 'support'.$uploader->getUploadedFilename();
                   
                    $fileUrl = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList')->getPath('media') . '/' .$upload_document;
                }catch (\Exception $e) {
                    $this->messageManager->addError(
                        __('File format not supported.')
                    );
                    $this->_redirect('support');
                    return;
                }
            }

            $data = [
                'subject' => $subject,
                'departmentId' => 136021000000006907,
                'contactId' => $customer['id'],
                'email' => $requestData['email'],
                'description' => $message,
                'classification'=> $classi,
                'cf' => $customField
            ];
            
            if(!empty($fileUrl)){
                $upload = $helper->uploadFile($fileUrl, $token, $orgId);
                $data['uploads'] = array($upload['id']);
            }
            $send = $helper->createTicket($data, $token, $orgId);

            if(isset($send['ticketNumber'])){
                $this->messageManager->addSuccessMessage(__('You sent the ticket.'));
            }else{
                $this->messageManager->addErrorMessage(
                    __('Something went wrong.!')
                );
            }
            return $this->_redirect('support');
    }
}
