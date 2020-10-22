<?php
 
namespace Magehit\Storelocator\Controller\Index;
 
use Magento\Framework\App\RequestInterface;
 
class Sendmail extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $dataHelper;
    protected $resultJsonFactory;
    protected $scopeConfig;
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magehit\Storelocator\Helper\Data $dataHelper,
         \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_request = $request;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
         $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
        $success =false;
        $message = __('Your inquiry is Not Sent.');
        if ($this->getRequest()->getPost())
        {   if($this->getRequest()->getPost('recipient')){
                $recipient = $this->getRequest()->getPost('recipient');
            }else{
                $recipient =  $this->_scopeConfig->getValue(
                    'trans_email/ident_general/email',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            }
            
            $name = $this->getRequest()->getPost('name');
            $email = $this->getRequest()->getPost('store_email');
            $telephone = $this->getRequest()->getPost('telephone');
            $comment = $this->getRequest()->getPost('comment');

            $store = $this->_storeManager->getStore()->getId();
            try
            {

                $templateVars = array(
                        'store' => $this->_storeManager->getStore(),
                        'name' => $name,
                        'email'   => $email,
                        'telephone' => $telephone,
                        'comment' =>$comment
                );
                $sento = trim($recipient);//$this->dataHelper->getSendto();
                $sender = $this->dataHelper->getSender();
                // var_dump($templateVars);
                //var_dump($sento);die;
                //var_dump($sender);die;
                $templatemail = $this->dataHelper->getConfig('template_email/template' ,$this->_storeManager->getStore()->getId());
                $transport = $this->_transportBuilder->setTemplateIdentifier($templatemail)
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store])
                    ->setTemplateVars($templateVars)
                    ->setFrom('general')
                    ->setReplyTo($email, $name)
                    ->addTo($sento, $sender)
                    ->getTransport();
                
                try{
                    $transport->sendMessage();
                }
                catch (Exception $e)
                {
                   $message = $e->getMessage();
                }
                $success =true; 
                $message = __('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.');
            }
            catch (Exception $e)
            {
                $message = $e->getMessage();
            }
        }
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => $success,'message'=>$message ]);

    }
}