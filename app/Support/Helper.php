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
    public static function  removeExtention($filename)
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
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

    public static function  toDateNumber(Carbon $date)
    {
        $strVal = $date->year - 1911;
        $strVal .= static::getMonthDayString($date->month,$date->day);

        return (int)$strVal;

    }

    public static function getMonthDayString(int $month, int $day)
    {
        $monthString= $month < 10 ? '0' . strval($month) : strval($month);
       
        $dayString= $day < 10 ? '0' . strval($day) : strval($day);
      
        return $monthString . $dayString;

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

   
    
    

   

}
