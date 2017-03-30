<?php
/**
 * Author: Denis Beliaev <cimmwolf@gmail.com>
 */

namespace DenisBeliaev\logAnalyzer;

/**
 * Class Parser
 * @package DenisBeliaev\logAnalyzer
 *
 * @property string $date
 * @property string $message
 * @property string $messageType
 */
class Parser
{
    public $lastModified;
    protected $string;
    protected $date;
    protected $message;
    protected $messageType;

    /**
     * @param $string
     */
    public function __construct($string)
    {
        $this->string = $string;
    }

    public function __get($name)
    {
        switch ($name) {
            default:
                $getter = 'get' . $name;
                if (method_exists($this, $getter))
                    return $this->$getter();
                else
                    return $this->{$name};
        }
    }

    public function getMessageType()
    {
        if (empty($this->messageType)) {
            $this->getMessage();
            $string = str_replace($this->message, '', $this->string);
            $matches = [];
            if (preg_match('/\[(error|info|warn(ing)?)\]/', $string, $matches)) {
                $this->messageType = str_replace('warning', 'warn', $matches[1]);
            } else
                $this->messageType = '?';
        }
        return $this->messageType;
    }

    public function getMessage()
    {
        if (empty($this->message)) {
            $date = $this->getDate();
            $matches = [];
            $datePattern = preg_quote($date);
            if (preg_match("~$datePattern.*\] (.*?)(,|$)~", $this->string, $matches))
                $this->message = $matches[1];
            else
                $this->message = false;
        }
        return $this->message;
    }

    public function getDate()
    {
        if (empty($this->date)) {
            $matches = [];
            if (preg_match('/\d{4}\/\d\d\/\d\d \d\d:\d\d:\d\d/', $this->string, $matches))
                $this->date = $matches[0];
            else
                $this->date = false;
        }
        return $this->date;
    }

    public function filterMessage($message)
    {
        $message = preg_replace(
            [
                '/\d+#\d+: (\*\d+ (FastCGI sent in stderr: )?)?/',
                '/PHP message: (PHP .*?:\s+)?/',
                '/in .*?( on line |:)\d+/',
                '/exception .*? with message /',
                '/PID=\d+/'
            ],
            '',
            $message
        );

        $message = preg_replace(
            '/cache entry [a-z0-9]+/',
            'cache entry',
            $message
        );

        $message = preg_replace(
            '~to a temporary file [a-zA-Z0-9_/]+~',
            'to a temporary file',
            $message
        );

        $message = str_replace(
            [
                ' while reading response header from upstream',
                ' while reading upstream'
            ], '', $message);

        $message = trim($message, " \t\n\r\0\x0B:\"'");
        return $message;
    }
}