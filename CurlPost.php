<?php
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
            throw new Exception('Принятые данные не JSON, код ошибки JSON: ' . json_last_error() . PHP_EOL .
                'Получены данные: ' . $responce . PHP_EOL);
        }
    }
}