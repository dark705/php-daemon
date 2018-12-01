<?php
class Daemon
{
	private $stop;
	private $sleep;
	
	public function __construct($file = 'daemon.pid', $sleep = 10)
    {
        if ($this->isDaemonActive($file)) {
            echo 'Daemon is already run!' . PHP_EOL;
            exit(0);
        }
		$this->stop = false;
        $this->sleep = $sleep;
        pcntl_signal(SIGTERM,[$this,'signalHandler']);
        file_put_contents($file, getmypid());
    }
	
    public function run($func){
        while(!$this->stop){ //exit by throw Exception or stop
			$func(); 
            sleep($this->sleep);
        }
    }
	
    public function signalHandler($signo) {
        switch($signo) {
            case SIGTERM:
                $this->stop = true;
                break;
        }
    }

    public function isDaemonActive($pid_file) {
        if( is_file($pid_file) ) {
            $pid = file_get_contents($pid_file);
            if(posix_kill($pid,0)) {
                return true;
            } else {
                if(!unlink($pid_file)) {
                    exit(-1);
                }
            }
        }
        return false;
    }
}