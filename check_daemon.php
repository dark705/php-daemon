#!/usr/bin/php
<?php
define("CONST_EMAIL", 'user@host');
define("CONST_URL", 'https://syn.su/testwork.php');
define("CONST_KEYXOR", null);
define("CONST_CHECKTIME", 60 * 60);
define("CONST_DEBUG", true);


require_once('CurlPost.php');
require_once('Daemon.php');
require_once('functions.php');

$check = function () {
	$curl = new CurlPost(CONST_URL, CONST_DEBUG);
	$responce = $curl->setParams('method=get')->getResponceJson();
	if (!array_key_exists('message response', $responce)){
		throw new Exception('В принятых данных не обнаружено свойство: "message response". Данные:' . print_r($responce, true) . PHP_EOL);
	}
	$message = base64_encode(xorEncoder($responce['message response'], CONST_KEYXOR));
	$responce = $curl->setParams('method=UPDATE&message=' . $message)->getResponceJson();
	if (!is_null($responce['errorCode']) || $responce['response'] != 'Success') {
		throw new Exception('Ошибка в errorCode или в response' .  PHP_EOL);
	} else {
		if(CONST_DEBUG){
			echo 'OK'. PHP_EOL;
            print_r($responce);
            echo  PHP_EOL;
        }
	}
};

try {
	$daemon = new Daemon(basename(__FILE__, '.php') . '.pid', CONST_CHECKTIME);
	//fclose(STDIN);
	//fclose(STDOUT);
	//fclose(STDERR);
	$daemon->run($check);
	
} catch (Exception $error) {
    $message = $error->getMessage();
    echo $message;
   //mail(CONST_EMAIL, 'Error', $message);
}

