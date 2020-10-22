<?php


namespace Magehit\Storepickup\Api\Data;

interface StorepickupInterface
{

    const STORE_NAME = 'store_name';
    const STOREPICKUP_ID = 'storepickup_id';
    const EMAIL = 'email';


    public function getStorepickupId();

    public function setStorepickupId($storepickupId);

    public function getStoreName();

    public function setStoreName($storeName);

    public function getEmail();
    public function setEmail($email);
}
