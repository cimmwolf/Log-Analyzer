<?php
/**
 * Author: Denis Beliaev <cimmwolf@gmail.com>
 */

namespace DenisBeliaev\logAnalyzer;

class Parser
{
    protected $filename;
    public $lastModified;

    /**
     * @param string $filename
     * @param $timezone
     */
    public function __construct($filename, $timezone = 'UTC')
    {
        date_default_timezone_set($timezone);

        if (filter_var($filename, FILTER_VALIDATE_URL) !== FALSE) {
            $file_headers = @get_headers($filename, 1);
            if (strpos(array_values($file_headers)[0], '200 OK') === false)
                throw new \RuntimeException("$filename does not exist!");
            if (isset($file_headers['Last-Modified']))
                $mtime = $file_headers['Last-Modified'];
            elseif (isset($file_headers['X-Last-Modified']))
                $mtime = $file_headers['X-Last-Modified'];
            if (isset($mtime))
                $this->lastModified = strtotime($mtime);
        } else {
            if (!file_exists($filename))
                throw new \RuntimeException("$filename does not exist!");
            $this->lastModified = filemtime($filename);
        }

        $this->filename = $filename;
    }

    /**
     * @param int $timestamp
     * @return bool
     */
    public function isUpdated($timestamp)
    {
        if (!is_int($timestamp)) {
            if ((is_numeric($timestamp) AND !is_float($timestamp)) OR is_bool($timestamp) OR is_null($timestamp))
                $timestamp = intval($timestamp);
            else
                throw new \InvalidArgumentException('$timestamp parameter must be integer!');
        }

        if ($this->lastModified > $timestamp)
            return true;
        return false;
    }

    public function parse()
    {
        $log = file($this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($log))
            return [];

        $patterns = [
            '~^[0-9/]{10}\s[0-9:]{8}\s\[.*?\]\s\[.*?\]\s.*$~',
            '~^[0-9/]{10}\s[0-9:]{8}\s\[.*?\]\s[0-9#]+?:\s.*?client:.*?request:.*?host:.*$~',
        ];
        $replacements = [
            '\DenisBeliaev\logAnalyzer\YiiLogParser',
            '\DenisBeliaev\logAnalyzer\NginxLogParser',
        ];
        $parserClass = preg_replace($patterns, $replacements, $log[0], 1);
        if($parserClass == $log[0])
            throw new \UnexpectedValueException('Unknown log type!');

        return $parserClass::parse($log);
    }
}