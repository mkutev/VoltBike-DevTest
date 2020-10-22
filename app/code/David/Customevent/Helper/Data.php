<?php
namespace David\Customevent\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Order\Address\Renderer;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_objectManager;
	protected $_timezoneInterface;
	protected $_registry;
	protected $_resultPageFactory;
	protected $_request;
	protected $_transportBuilder;
	protected $_fileSystem;
	protected $_storeManager;
    /**
     * Block constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		Filesystem $fileSystem,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
		$this->_objectManager 		= $objectManager;
        $this->_registry 			= $registry;
		$this->_timezoneInterface 	= $timezoneInterface;
		$this->_resultPageFactory 	= $resultPageFactory;
		$this->_request 			= $request;
		$this->_transportBuilder 	= $transportBuilder;
		$this->_fileSystem 			= $fileSystem;
		$this->_storeManager 		= $storeManager;
        parent::__construct($context);
    }
	
	public function getUrlBilder($path = ''){
		 return $this->_urlBuilder->getUrl($path);
	}
	
	public function getStore(){
		return $this->_storeManager->getStore();
	}
	
	public function getBaseUrl($path = '') {
		return $this->getStore()->getBaseUrl() . $path;
	}
	
	public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getToken(){
        $ch = curl_init('https://accounts.zoho.com/apiauthtoken/nb/create');
        $data = 'SCOPE=ZohoSupport/supportapi&EMAIL_ID=sales@voltbike.ca&PASSWORD=aggression';
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);   
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result =  curl_exec($ch);
        preg_match('/AUTHTOKEN=(.*?)\sRESULT/', $result, $matches);
        return trim($matches[1]);
    }
    
    
    public function getOrgId($token){
        $ch = curl_init('https://desk.zoho.com/api/v1/organizations');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Zoho-authtoken ".$token));
        $result = json_decode(curl_exec($ch),true);
        return $result['data'][0]['id'];
    }
    
    public function createTicket($data, $token, $orgId){
        $ch = curl_init('https://desk.zoho.com/api/v1/tickets');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));   
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("orgId: $orgId", "Authorization: Zoho-authtoken $token"));
        return json_decode(curl_exec($ch), true);
    }

    function createCustomer($name, $token, $orgId){
        $ch = curl_init('https://desk.zoho.com/api/v1/contacts');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = [
            'lastName' => $name
        ];
        $data = json_encode($data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);   
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("orgId: $orgId", "Authorization: Zoho-authtoken $token"));
        return json_decode(curl_exec($ch), true);
    }

    public function uploadFile($file, $token, $orgId){
        $files['file*'.basename($file)] = file_get_contents($file);
        $curl = curl_init('https://desk.zoho.com/api/v1/uploads');
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;
        $post_data = $this->build_data_files($boundary, $files);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
              "Content-Type: multipart/form-data; boundary=" . $delimiter,
              "Content-Length: " . strlen($post_data),
              "orgId: $orgId", "Authorization: Zoho-authtoken $token"
            ),
        ));
        return json_decode(curl_exec($curl),true);
    }
    
    
    
    public function build_data_files($boundary, $files){
        $data = '';
        $eol = "\r\n";
        $delimiter = '-------------' . $boundary;
        foreach ($files as $name => $content) {
            $_name = explode('*', $name);
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $_name[0] . '"; filename="' . $_name[1] . '"' . $eol
                // . 'Content-Type: image/png'.$eol
                . 'Content-Transfer-Encoding: binary'.$eol
                ;
    
            $data .= $eol;
            $data .= $content . $eol;
        }
        $data .= "--" . $delimiter . "--".$eol;
        return $data;
    }
}