<?php
function xorEncoder($str, $key = null){
    if (is_null($key)) {
        return $str;
    } else {
        $l = strlen($str);
        $k = strlen($key);
        $r = '';
        for($i = 0; $i < $l; $i++){
            $r .= $str[$i] ^ $key[$i % $k];
        }
        return $r;
    }
}