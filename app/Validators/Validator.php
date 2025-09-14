<?php

namespace StorageUnit\Validators;

class Validator{
    
    function noTagsSpaces($string){
        $string = trim($string);
        $string = strip_tags($string);
        return $string;
    }
}