<?php
class CurlPost{
    private $url;
    private $debug;
    private $curlopt;

    public function __construct($url = null, $options = null, $debug = false){
        $this->url = $url;
		if (!$options){
			$this->curlopt = [
				CURLOPT_FAILONERROR => true,
				CURLOPT_URL => $url,
				CURLOPT_HEADER => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
			];
		} else {
			$this->curlopt =  $curlopt;
		}
		$this->debug = $debug;
    }

    public function setFields($fields){
        $this->curlopt[CURLOPT_POSTFIELDS] = $fields;
        return $this;
    }

    public function getResponce(){
        $ch = curl_init();
        curl_setopt_array($ch, $this->curlopt);
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