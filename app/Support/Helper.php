<?php

namespace App\Support;


use Carbon\Carbon;

class Helper 
{
    public static function  str_starts_with($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }
    public static function str_ends_with($haystack, $needle)
    {
        return strrpos($haystack, $needle) + strlen($needle) ===
            strlen($haystack);
    }

    public static function isTrue($val)
    {
        if(is_null($val)) return false;
        
        if(is_numeric($val)){
            if($val) return true;
            return false;
        }
        if( is_string($val) ){
            $val = strtolower($val);
            return $val=='true';
        } 

        return (bool)$val;
    }

    public static function array_has_dupes($array) 
    {
        return count($array) !== count(array_unique($array));
    }
    
   
    public static function strFromArray(Array $arrVal,$split=',')
    {
        if(!count($arrVal)) return '';
        $str='';
        for($i = 0; $i < count($arrVal); ++$i) {
            
            if($arrVal[$i]){
                if($i>0){
                    $str .= ',' ;
                }
                $str .=  $arrVal[$i];
            }
           
        }

        return $str;
    }

    public static function removeExtention($filename)
    {
        return preg_replace('/.[^.]*$/', '', $filename);
    }
    
    

   

}
