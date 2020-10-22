<?php


namespace Magehit\Storepickup\Model;

use Magehit\Storepickup\Api\Data\StorepickupInterface;

class Storepickup extends \Magento\Framework\Model\AbstractModel implements StorepickupInterface
{

    protected $_eventPrefix = 'magehit_storepickup_storepickup';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magehit\Storepickup\Model\ResourceModel\Storepickup');
    }

    /**
     * Get storepickup_id
     * @return string
     */
    public function getStorepickupId()
    {
        return $this->getData(self::STOREPICKUP_ID);
    }

    /**
     * Set storepickup_id
     * @param string $storepickupId
     * @return \Magehit\Storepickup\Api\Data\StorepickupInterface
     */
    public function setStorepickupId($storepickupId)
    {
        return $this->setData(self::STOREPICKUP_ID, $storepickupId);
    }

    /**
     * Get store_name
     * @return string
     */
    public function getStoreName()
    {
        return $this->getData(self::STORE_NAME);
    }

    /**
     * Set store_name
     * @param string $storeName
     * @return \Magehit\Storepickup\Api\Data\StorepickupInterface
     */
    public function setStoreName($storeName)
    {
        return $this->setData(self::STORE_NAME, $storeName);
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Set email
     * @param string $email
     * @return \Magehit\Storepickup\Api\Data\StorepickupInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }
}
