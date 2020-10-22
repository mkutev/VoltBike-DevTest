<?php
namespace David\Customevent\Model\Carrier\Tablerate;

class RateQuery{
    public function afterGetBindings($subject, $result){
        if(!isset($result[':postcode_prefix'])){
            preg_match("/^(.+)-(.+)$/", $result[':postcode'], $zipParts);
            if(count($zipParts)){
                $result[':postcode_prefix'] = $zipParts[1];
            }else{
                $result[':postcode_prefix'] = $result[':postcode'];
            }
        }
        return $result;
    }
}