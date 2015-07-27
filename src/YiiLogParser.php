<?php
namespace DenisBeliaev\logAnalyzer;

class YiiLogParser
{
    public static function parse($log)
    {
        $output = [];
        foreach ($log as $key => $string) {
            $matches = [];
            if (!preg_match('~([0-9/]{10}\s[0-9:]{8})\s\[(.*?)\]\s\[.*?\]\s(.*)~', $string, $matches))
                continue;

            $row = ['message' => $matches[3]];
            $row['logTime'] = strtotime($matches[1]);
            $row['level'] = $matches[2];
            $output[] = $row;
        }
        return $output;
    }
}