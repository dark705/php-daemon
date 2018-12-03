#!/usr/bin/php
<?php
define("CONST_EMAIL", 'user@host');
define("CONST_URL", 'https://syn.su/testwork.php');
define("CONST_CHECKTIME", 60 * 60);
define("CONST_DEBUG", true);

$child_pid = pcntl_fork();
if( $child_pid ) {
    exit(0);
}
posix_setsid();
declare(ticks=1);

require_once(__DIR__.'/ClassesAndFunctions/CurlPost.php');
require_once(__DIR__.'/ClassesAndFunctions/Daemon.php');
require_once(__DIR__.'/ClassesAndFunctions/functions.php');

$check = function () {
	$curl = new CurlPost(CONST_URL, null, CONST_DEBUG);
	$data = $curl->setFields('method=get')->getResponceJson();
	if (!isset($data['response']['message']) || !isset($data['response']['key'])){
		throw new Exception('В принятых данных не обнаружено: response.message или response.key. Данные:' . print_r($data, true) . PHP_EOL);
	}
	$message = base64_encode(xorEncoder($data['response']['message'], $data['response']['key']));
    $data = $curl->setFields('method=UPDATE&message=' . $message)->getResponceJson();
	if (!isset($data['response']) || ($data['response'] != "Success")) {
		throw new Exception('Ошибка response' .  PHP_EOL);
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
	if (!CONST_DEBUG) {
		fclose(STDIN);
		fclose(STDOUT);
		fclose(STDERR);
    }
	$daemon->run($check);
} catch (Exception $error) {
    $message = $error->getMessage();
	if (CONST_DEBUG) {
		echo $message;
	} else {
		mail(CONST_EMAIL, 'Error on PHP check daemon', $message);
	}
}
