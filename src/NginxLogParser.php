<?php
namespace DenisBeliaev\logAnalyzer;

class NginxLogParser
{
    public static function parse($log)
    {
        $output = [];
        foreach ($log as $key => $string) {
            $matches = [];
            if(!preg_match('~([0-9/]{10}\s[0-9:]{8})\s\[(.*?)\]\s[0-9#]+?:\s(.*?client:.*?request:.*?host:.*)~', $string, $matches))
                throw new \UnexpectedValueException('Wrong format of string № ' . ($key + 1));

            $row = ['message' => $matches[3]];
            $row['logTime'] = strtotime($matches[1]);
            $row['level'] = $matches[2];
            $output[] = $row;
        }
        return $output;
    }
}