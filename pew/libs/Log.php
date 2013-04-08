<?php 

namespace pew\libs;

/**
 * Log class
 * 
 * Logs activities, including timestamps and levels.
 * 
 * @package pew/libs
 * @author ifcanduela <ifcanduela@gmail.com>
*/
class Log
{
    /**
     * Error level constants.
     */
    const DEBUG = 0;
    const INFO  = 2;
    const ALERT = 4;
    const ERROR = 6;
    const FATAL = 8;
    const OFF   = 10;

    /**
     * String labels for the error level constants
     * 
     * @var array
     */
    private $level_names = array(
            self::DEBUG => 'DEBUG',
            self::INFO =>  'INFO',
            self::ALERT => 'ALERT',
            self::ERROR => 'ERROR',
            self::FATAL => 'FATAL',
            self::OFF   => '',
        );

    /**
     * String labels for the error level constants.
     * 
     * @var array
     */
    private $log = array();
    
    /**
     * String format for the date field in the lof.
     * 
     * @var string
     */
     private $date_format = 'Y-m-d';
    
    /**
     * String format for the date field in the lof.
     * 
     * @var string
     */
    private $time_format = 'H:m:s';

    /**
     * Flag that determines if a log file is created when the object is destroyed.
     * @var boolean
     */
    private $auto_dump = false;

    /**
     * Folder name where the log files are saved.
     * 
     * @var string
     */
    private $log_folder = 'logs';
    
    /**
     * File name for the log output.
     * 
     * @var string
     */
    private $log_filename = null;

    /**
     * Class constructor.
     *
     * @param string $filename    Name of the log file
     * @param string $date_format Date format
     * @param string $time_format Time format
     */
    function __construct($level, $filename = null)
    {
        $this->level($level);

        if (!is_string($filename)) {
            $filename = date('Y-m-d') . 'log.txt';
        }
    }

    function __destruct()
    {
        if ($this->auto_dump()) {
            $this->dump_to_file();
        }
    }
    
    public function auto_dump($auto_dump = null)
    {
        if (!is_null($auto_dump)) {
            $this->auto_dump = $auto_dump;
        }

        return $this->auto_dump;
    }

    protected function log($message, $level = self::INFO)
    {
        $time = time();

        $this->log[$time] = array(
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
        if (array_key_exists($level, $this->level_names)) {
            return $this->level_names[$level];
        } else {
            return null;
        }
    }

    public function date_format($format = null)
    {
        if (is_string($format)) {
            $this->date_format = $format;
        }
        
        return $this->date_format;
    }

    public function dump()
    {
        
    }

    public function dump_to_file($clear = true)
    {
        if (count($this->log) > 0) {
            if (!is_dir(dirname($this->log_file()))) {
                mkdir(dirname($this->log_file()));
            }

            $entries = array();

            foreach ($this->log as $entry) {
                if ($entry['level'] > $this->level()) {
                    extract($entry);
                    $entries[] = "[{$date} {$time}] --{$level}-- {$message}" . PHP_EOL;
                }
            }

            if (!empty($entries)) {
                file_put_contents($this->log_file(), join('', $entries), FILE_APPEND);
            }

            if ($clear) {
                $this->log = array();
            }
        }
    }

    public function level($level = null)
    {
        if (is_numeric($level)) {
            $this->log_level = $level;
        }

        return $this->log_level;
    }

    public function log_file($filename = null)
    {
        if (is_string($filename)) {
            $this->log_folder = dirname($filename);
            $this->log_filename = basename($filename);
        }
        
        return $this->log_folder . DIRECTORY_SEPARATOR . $this->log_filename;
    }

    public function time_format($format = null)
    {
        if (is_string($format)) {
            $this->time_format = $format;
        }

        return $this->time_format;
    }
}
