<?php
namespace Magehit\Storelocator\Model;

use Magehit\Storelocator\Api\Data\StorelocatorInterface;

class Storelocator extends \Magento\Framework\Model\AbstractModel implements StorelocatorInterface
{

    protected function _construct()
    {
        $this->_init('Magehit\Storelocator\Model\ResourceModel\Storelocator');
    }

    public function getStorelocatorId()
    {
        return $this->getData(self::STORELOCATOR_ID);
    }

    public function setStorelocatorId($storelocatorId, $storelocator_id)
    {
        return $this->setData(self::STORELOCATOR_ID, $storelocatorId);

        return $this->setData(self::STORELOCATOR_ID, $storelocator_id);
    }

    public function getStoreName()
    {
        return $this->getData(self::STORE_NAME);
    }

    public function setStoreName($store_name)
    {
        return $this->setData(self::STORE_NAME, $store_name);
    }

    public function getStoreThumnail()
    {
        return $this->getData(self::STORE_THUMNAIL);
    }

    public function setStoreThumnail($store_thumnail)
    {
        return $this->setData(self::STORE_THUMNAIL, $store_thumnail);
    }

    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }


    public function getWebsite()
    {
        return $this->getData(self::WEBSITE);
    }

    public function setWebsite($website)
    {
        return $this->setData(self::WEBSITE, $website);
    }

    public function getLat()
    {
        return $this->getData(self::LAT);
    }


    public function setLat($lat)
    {
        return $this->setData(self::LAT, $lat);
    }


    public function getLng()
    {
        return $this->getData(self::LNG);
    }


    public function setLng($lng)
    {
        return $this->setData(self::LNG, $lng);
    }


    public function getStreet()
    {
        return $this->getData(self::STREET);
    }


    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }


    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }


    public function getRegion()
    {
        return $this->getData(self::REGION);
    }


    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }


    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }


    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }


    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }


    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }


    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }


    public function getFax()
    {
        return $this->getData(self::FAX);
    }


    public function setFax($fax)
    {
        return $this->setData(self::FAX, $fax);
    }


    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }


    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }


    public function getProductIds()
    {
        return $this->getData(self::PRODUCT_IDS);
    }

    public function setProductIds($product_ids)
    {
        return $this->setData(self::PRODUCT_IDS, $product_ids);
    }
}
