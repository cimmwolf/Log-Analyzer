<?php
namespace DenisBeliaev\logAnalyzer;

class NginxLogParser
{
    public static function parse($log)
    {
        $output = [];
        foreach ($log as $key => $string) {
            $matches = [];
            if (!preg_match('~([0-9/]{10} [0-9:]{8}) \[(.*?)\] \d+#\d+: \*\d+ (.*)$~', $string, $matches))
                continue;

            $row = ['message' => self::filter($matches[3])];
            $row['logTime'] = strtotime($matches[1]);
            $row['level'] = str_replace('warning', 'warn', $matches[2]);
            $output[] = $row;
        }
        return $output;
    }

    /**
     * Filter for messages
     * @param $str string
     * @return string
     */
    private static function filter($str)
    {
        if (strpos($str, 'client: ') !== false)
            $str = self::filterClients($str);

        if (strpos($str, 'to a temporary file') !== false)
            $str = self::filterTempFiles($str);
        return $str;
    }

    /**
     * Remove client IP information
     * @param $str
     * @return string
     */
    private static function filterClients($str)
    {
        return preg_replace('~(.*?) client:.*?,(.*)~', '$1$2', $str);
    }

    /**
     * Remove temporary files paths
     * @param $str
     * @return string
     */
    private static function filterTempFiles($str)
    {
        return preg_replace('~(.*? to a temporary file) [a-zA-Z0-9_/]+( .*)~', '$1$2', $str);
    }
}