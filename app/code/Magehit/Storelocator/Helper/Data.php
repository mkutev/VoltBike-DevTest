<?php
namespace Magehit\Storelocator\Helper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_STORE = 'store_locator/';
    protected $_backendUrl;
    protected $storeManager;
    protected $request;
    protected $assetRepo;
    protected $_mediaDirectory;
    protected $_imageFactory;
    //protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface  $RequestInterface,
        \Magento\Framework\View\Asset\Repository $Repository,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Image\AdapterFactory $imageFactory
		

    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->request = $RequestInterface;
        $this->assetRepo = $Repository;
		$this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
		$this->_imageFactory = $imageFactory;
    }

    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('magehit_storelocator/contacts/products', ['_current' => true]);
    }
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }
    
    public function getUrlimage($path){
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path;
    }
   

    public function getConfig($code, $storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_STORE . $code, $storeId);
    }
    public function getEnable($storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_STORE . 'general/enable', $storeId);
    }
    public function getIdentifier($storeId = null){
         return $this->getConfigValue(self::XML_PATH_STORE . 'general/url', $storeId);
    }
    
    public function getUrlSuffix($storeId = null){
         return $this->getConfigValue(self::XML_PATH_STORE . 'general/suffix', $storeId);    
    }
    public function getSendto(){
        return $this->getConfigValue('trans_email/ident_general/email');
        
    }
    public function getSender(){
        return $this->getConfigValue('trans_email/ident_general/name');
        
    }
    public function getUrlstore($path,$storeId = null){
        return $this->storeManager->getStore()->getBaseUrl().$this->getIdentifier($storeId).'/'.$path;
    }
    public function getLink(){
        $path = trim($this->getConfig( 'general/url',$this->storeManager->getStore()->getId())).trim($this->getConfig( 'general/suffix',$this->storeManager->getStore()->getId()));
        return $this->storeManager->getStore()->getBaseUrl().$path;
    }
    public function getLabel(){
        return trim($this->getConfig( 'general/label_link',$this->storeManager->getStore()->getId()));
         
    }

    public function getDefaultimage(){
        if(trim($this->getConfig( 'general/logo',$this->storeManager->getStore()->getId())) != ''){
            return $this->getUrlimage('storelocator/'.trim($this->getConfig( 'general/logo',$this->storeManager->getStore()->getId())));
        }
        $params = array('_secure' => $this->request->isSecure());
        return $this->assetRepo->getUrlWithParams('Magehit_Storelocator::images/store.png', $params);
       
    }
    public function getMaxradius(){
        $max = (int)trim($this->getConfig( 'radius/max',$this->storeManager->getStore()->getId()));
        if(!$max){
            return 100;
        }else{
            return $max;
        }
    }
	
	public function getPhotoMediaFolder(){
		$folder = "community_photos";
		return $folder;
	}
	
	protected function _fileExists($filename)
    {
        if ($this->_mediaDirectory->isFile($filename)) {
            return true;
        }
        return false;
    }

    /**
     * Resize image
     * @return string
     */
    public function resize($image, $width = null, $height = null)
    {
        $mediaFolder = $this->getPhotoMediaFolder();

        $path = $mediaFolder . '/cache';
        if ($width !== null) {
            $path .= '/' . $width . 'x';
            if ($height !== null) {
                $path .= $height ;
            }
        }

        $absolutePath = $this->_mediaDirectory->getAbsolutePath($mediaFolder) . $image;
        $imageResized = $this->_mediaDirectory->getAbsolutePath($path) . $image;

        if (!$this->_fileExists($path . $image)) {
            $imageFactory = $this->_imageFactory->create();
            $imageFactory->open($absolutePath);
            $imageFactory->constrainOnly(true);
            $imageFactory->keepTransparency(true);
            $imageFactory->keepFrame(false);
            $imageFactory->keepAspectRatio(true);
            $imageFactory->resize($width, $height);
            $imageFactory->save($imageResized);
        }

        return $path . $image;
    }
	
	public function randomNumber($length) {
		$result = '';

		for($i = 0; $i < $length; $i++) {
			$result .= mt_rand(0, 9);
		}

		return $result;
	}
	
	public function getAllowCountry(){
		$objectManager	= \Magento\Framework\App\ObjectManager::getInstance();        
		$countryHelper 	= $objectManager->get('Magento\Directory\Model\Config\Source\Country'); 
		$countryFactory = $objectManager->get('Magento\Directory\Model\CountryFactory');
		$allowCountry 	= $objectManager->get('Magento\Directory\Model\AllowedCountries');
		$countries = $countryHelper->toOptionArray();
		$allowData = array();
		foreach ( $countries as $countryKey => $country ) {

			if ($country['value'] != '' && in_array($country['value'],$allowCountry->getAllowedCountries())) { 
				$allowData[$country['value']] = $country;
				$stateArray = $countryFactory->create()->setId(
					$country['value']
				)->getLoadedRegionCollection()->toOptionArray();

				if ( count($stateArray) > 0 ) { //Again ignore empty values
					$allowData[$country['value']]['states'] = $stateArray;
				}

			}
		}
		return $allowData;
	}
	
	public function getStateByCountry($countryCode = 'CA'){
		$allowCountry = $this->getAllowCountry();
		return $allowCountry[$countryCode]['states'];
	}
	
	public function loadCountryByCode($code){
		$objectManager	= \Magento\Framework\App\ObjectManager::getInstance();   
		$country = $objectManager->get('\Magento\Directory\Model\CountryFactory')->create()->loadByCode($code);
        return $country->getName();
	}
}