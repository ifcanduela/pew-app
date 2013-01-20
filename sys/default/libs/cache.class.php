<?php

/**
 * This class can save PHP data to files and retrieve said data.
 *
 * @author Igor F. Canduela <ifcanduela@gmail.com>
 */
class Cache
{
    /**
     * @var integer Seconds to cache data
     */
    private $interval = 0;

    /**
     * @var string Location of cache files
     */
    private $folder = '';

    /**
     * @var boolean Use gzip compression in cache files
     */
    private $gzip = true;

    /**
     * Create a cache manager.
     * 
     * @param integer $seconds Defaults to 12 hours
     */
    public function __construct($seconds = 43200, $folder = 'cache')
    {
        $this->folder = $folder;
        $this->interval($seconds);

        if (!is_dir($this->folder)) {
            mkdir($this->folder, 0777, true);
        }
    }

    public function gzip($gzip = null)
    {
        if (!is_null($gzip)) {
            $this->gzip = $gzip;
        }

        return $this->gzip;
    }

    /**
     * Configure the default interval.
     * 
     * @param integer $seconds Seconds
     * @return integer Seconds
     */
    public function interval($seconds = null)
    {
        if (!is_null($seconds)) {
            $this->interval = $seconds;
        }

        return $this->interval;
    }

    /**
     * Checks if the file is cached.
     * 
     * @param string $filename Cache file
     * @param integer $interval Number of seconds
     * @return boolean True is the file is in the cache, false otherwise
     */
    public function cached($filename, $interval = null)
    {
        $file = $this->folder . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($file)) {
            return false;
        }

        if (is_null($interval) || !is_numeric($interval)) {
            $interval = $this->interval();
        }

        $filedate = filemtime($file);

        return (time() - $filedate) < $interval;
    }

    
    public function set($filename, $data)
    {
        $file = $this->folder. DIRECTORY_SEPARATOR . $filename;
        $serialized_data = serialize($data);

        if ($this->gzip) {
            $serialized_data = gzencode($serialized_data);
        }

        return file_put_contents($file, $serialized_data);
    }

    public function get($filename)
    {
        $file = $this->folder . DIRECTORY_SEPARATOR . $filename;
        
        if (!file_exists($file)) {
            throw new Exception("FileNotFound: {$file}");
        }

        $serialized_data = file_get_contents($file);

        if ($this->gzip) {
            $serialized_data = gzdecode($serialized_data);
        }

        return unserialize($serialized_data);
    }
}
