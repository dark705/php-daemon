#!/usr/bin/php

<?php
define("CONST_EMAIL", 'user@host');
define("CONST_URL", 'https://syn.su/testwork.php');
define("CONST_DEBUG", true);

class CurlPost{
    private $url;
    private $debug;
    private $options;

    public function __construct($url, $debug = false){
        $this->url = $url;
        $this->debug = $debug;
    }

    public function setParams($params){
        $this->options = [
            CURLOPT_FAILONERROR => true,
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
        ];
        return $this;
    }

    public function getResponce(){
        $ch = curl_init();
        curl_setopt_array($ch, $this->options);
        $responce = curl_exec($ch);
        if($this->debug){
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                print_r(curl_getinfo($ch));
                echo  PHP_EOL;
        }
        $error = curl_error($ch);
        curl_close($ch);
        if ($error){
            throw new Exception('Ошибка при получение данных cURL: ' . $error . PHP_EOL);
        }
        return $responce;
    }

    public function getResponceJson(){
        $responce = $this->getResponce();
        $json_responce = json_decode($responce, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return  $json_responce;
        } else {
            throw new Exception('Ошибка разбора данных JSON : ' . json_last_error() . PHP_EOL .
                'Полученые данные: ' . $responce . PHP_EOL);
        }
    }
}



function xorEncoder($str, $key = null){
    if (is_null($key)) {
        return $str;
    } else {

    }
}

try {
    $curl = new CurlPost(CONST_URL, true);
    $responce = $curl->setParams('method=get')->getResponceJson();
    if (!array_key_exists('message response', $responce)){
        throw new Exception('В принятых данных не обнаружено свойство: "message response". Данные:' . print_r($responce, true) . PHP_EOL);
    }

    $message = base64_encode(xorEncoder($responce['message response'], null));
    $responce = $curl->setParams('method=UPDATE&message=' . $message)->getResponceJson();

    if (!is_null($responce['errorCode']) || $responce['response'] != 'Success') {
        throw new Exception('Ошибка в принятых данных' .  PHP_EOL);
    }
} catch (Exception $error) {
    $message = $error->getMessage();
    echo $message;
   //mail(CONST_EMAIL, 'Error', $message);
}


