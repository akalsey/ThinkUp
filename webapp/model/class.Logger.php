<?php
class Logger {
    // our singleton instance
    private static $instance;
    var $log;
    var $network_username;

    function __construct($location) {
        $this->log = $this->openFile($location, 'a'); # Append to any prior file
    }

    // The singleton method
    public static function getInstance() {
        $config = Config::getInstance();
        if (!isset(self::$instance)) {
            self::$instance = new Logger($config->getValue('log_location'));
        }
        return self::$instance;
    }

    function setUsername($uname) {
        $this->network_username = $uname;
    }

    function logStatus($status_message, $classname) {
        $status_signature = date("Y-m-d H:i:s", time())." | ".(string) number_format(round(memory_get_usage() / 1024000, 2), 2)." MB | $this->network_username | $classname:";
        if (strlen($status_message) > 0) {
            $this->writeFile($this->log, $status_signature.$status_message); # Write status to log
        }
    }

    private function addBreaks() {
        $this->writeFile($this->log, ""); # Add a little whitespace
    }

    function close() {
        $this->addBreaks();
        $this->closeFile($this->log);
        self::$instance = null;
    }

    function openFile($filename, $type) {
        if (array_search($type, array('w', 'a')) < 0) {
            $type = 'w';
        }
        $filehandle = null;
        if (is_writable($filename)) {
            $filehandle = fopen($filename, $type);// or die("can't open file $filename");
        }
        return $filehandle;
    }

    function writeFile($filehandle, $message) {
        if (isset($filehandle)) {
            return fwrite($filehandle, $message."\n");
        }
    }

    function closeFile($filehandle) {
        if (isset($filehandle)) {
            return fclose($filehandle);
        }
    }

    function deleteFile($filename) {
        return unlink($filename);
    }
}
?>
