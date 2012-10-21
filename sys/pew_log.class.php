<?php 

/**
* Class PewLog
* 
* Logs activities, including timestamps and levels.
* 
* @author ifcanduela <ifcanduela@gmail.com>
*/
class PewLog
{
    const DEBUG = 0;
    const INFO  = 2;
    const ALERT = 4;
    const ERROR = 6;
    const FATAL = 8;

    /**
     * String labels for the error level constants
     * 
     * @var array
     * @access protected
     */
    protected $_level_names = array(
            self::DEBUG => 'DEBUG',
            self::INFO =>  'INFO',
            self::ALERT => 'ALERT',
            self::ERROR => 'ERROR',
            self::FATAL => 'FATAL',
        );

    /**
     * String labels for the error level constants.
     * 
     * @var array
     * @access protected
     */
    protected $_log = array();
    
    /**
     * String format for the date field in the lof.
     * 
     * @var string
     * @access protected
     */
     protected $_date_format = 'Y-m-d';
    
    /**
     * String format for the date field in the lof.
     * 
     * @var string
     * @access protected
     */
    protected $_time_format = 'H:m:s';

    /**
     * Folder name where the log files are saved.
     * 
     * @var string
     * @access protected
     */
    protected $_log_folder = 'logs';
    
    /**
     * File name for the log output.
     * 
     * @var string
     * @access protected
     */
    protected $_log_filename = null;

    function __construct($filename = null, $date_format = null, $time_format = null)
    {
        $this->_log_filename = date('Y-m-d') . '.txt';

        $this->log_file($filename);
        $this->date_format($date_format);
        $this->time_format($time_format);
    }

    function __destruct()
    {
        $this->dump();
    }

    protected function log($message, $level = self::INFO)
    {
        $time = time();

        $this->_log[$time] = array(
                'level' => $this->get_level_name($level),
                'message' => $message,
                'date' => date($this->date_format(), $time),
                'time' => date($this->time_format(), $time),
            );
    }

    public function alert($message)
    {
        $this->log($message, self::ALERT);
    }

    public function debug($message)
    {
        $this->log($message, self::DEBUG);        
    }

    public function error($message)
    {
        $this->log($message, self::ERROR);
    }

    public function fatal($message)
    {
        $this->log($message, self::FATAL);
    }

    public function info($message)
    {
        $this->log($message, self::INFO);
    }

    public function get_level_name($level)
    {
        if (array_key_exists($level, $this->_level_names)) {
            return $this->_level_names[$level];
        } else {
            return null;
        }
    }

    public function log_file($filename = null)
    {
        if (is_string($filename)) {
            $this->_log_folder = dirname($filename);
            $this->_log_filename = basename($filename);
        }
        
        return $this->_log_folder . DIRECTORY_SEPARATOR . $this->_log_filename;
    }

    public function time_format($format = null)
    {
        if (is_string($format)) {
            $this->_time_format = $format;
        }

        return $this->_time_format;
    }

    public function date_format($format = null)
    {
        if (is_string($format)) {
            $this->_date_format = $format;
        }
        
        return $this->_date_format;
    }

    public function dump($clear = true)
    {
        if (count($this->_log) > 0) {
            if (!is_dir(dirname($this->log_file()))) {
                mkdir(dirname($this->log_file()));
            }

            $entries = array();

            foreach ($this->_log as $entry) {
                extract($entry);
                $entries[] = "[{$date} {$time}] --{$level}-- {$message}" . PHP_EOL;
            }

            file_put_contents($this->log_file(), join('', $entries), FILE_APPEND);

            if ($clear) {
                $this->_log = array();
            }
        }
    }
}
