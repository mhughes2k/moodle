<?php
namespace core_ai;

class logger {
    protected $logpath;
    function __construct($identifier) {
        $logdir = make_temp_directory('ai', true);
        $this->logpath = $logdir . '/' . $identifier . '.log';
    }
    public function write($message) {
        $ts = microtime(true);
        $f = fopen($this->logpath, 'a');
        if(flock($f, LOCK_EX | LOCK_NB)) {
            fwrite($f, "{$ts} - {$message}\n");
            flock($f, LOCK_UN);
        }
        fclose($f);
    }
}
