<?php
namespace Magehit\Storelocator\Model\Import\StoreImport;
interface RowValidatorInterface extends \Magento\Framework\Validator\ValidatorInterface
{
       const ERROR_URL_IS_EMPTY= 'InvalidValueURL';
    
    public function init($context);
}