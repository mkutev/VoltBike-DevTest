<?php


namespace Magehit\Storelocator\Api\Data;

interface StorelocatorInterface
{

    const WEBSITE = 'website';
    const COUNTRY = 'country';
    const CONTENT = 'content';
    const LAT = 'lat';
    const LNG = 'lng';
    const REGION = 'region';
    const STREET = 'street';
    const TELEPHONE = 'telephone';
    const STORE_THUMNAIL = 'store_thumnail';
    const EMAIL = 'email';
    const FAX = 'fax';
    const PRODUCT_IDS = 'product_ids';
    const POSTCODE = 'postcode';
    const STORELOCATOR_ID = 'storelocator_id';
    const STORE_NAME = 'store_name';
    const CITY = 'city';


    /**
     * Get storelocator_id
     * @return string|null
     */
    public function getStorelocatorId();

    /**
     * Set storelocator_id
     * @param string $storelocator_id
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setStorelocatorId($storelocatorId, $storelocator_id);

    /**
     * Get store_name
     * @return string|null
     */
    public function getStoreName();

    /**
     * Set store_name
     * @param string $store_name
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setStoreName($store_name);

    /**
     * Get store_thumnail
     * @return string|null
     */
    public function getStoreThumnail();

    /**
     * Set store_thumnail
     * @param string $store_thumnail
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setStoreThumnail($store_thumnail);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setEmail($email);

    /**
     * Get website
     * @return string|null
     */
    public function getWebsite();

    /**
     * Set website
     * @param string $website
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setWebsite($website);

    /**
     * Get lat
     * @return string|null
     */
    public function getLat();

    /**
     * Set lat
     * @param string $lat
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setLat($lat);

    /**
     * Get lng
     * @return string|null
     */
    public function getLng();

    /**
     * Set lng
     * @param string $lng
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setLng($lng);

    /**
     * Get street
     * @return string|null
     */
    public function getStreet();

    /**
     * Set street
     * @param string $street
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setStreet($street);

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setCity($city);

    /**
     * Get region
     * @return string|null
     */
    public function getRegion();

    /**
     * Set region
     * @param string $region
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setRegion($region);

    /**
     * Get postcode
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set postcode
     * @param string $postcode
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setPostcode($postcode);

    /**
     * Get country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set country
     * @param string $country
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setCountry($country);

    /**
     * Get telephone
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set telephone
     * @param string $telephone
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setTelephone($telephone);

    /**
     * Get fax
     * @return string|null
     */
    public function getFax();

    /**
     * Set fax
     * @param string $fax
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setFax($fax);

    /**
     * Get content
     * @return string|null
     */
    public function getContent();

    /**
     * Set content
     * @param string $content
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setContent($content);

    /**
     * Get product_ids
     * @return string|null
     */
    public function getProductIds();

    /**
     * Set product_ids
     * @param string $product_ids
     * @return \Magehit\Storelocator\Api\Data\StorelocatorInterface
     */
    public function setProductIds($product_ids);
}
