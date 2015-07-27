<?php
/**
 * Author: Denis Beliaev <cimmwolf@gmail.com>
 */

$options = getopt("s:n:t:");
if (!isset($options['s'], $options['n']))
    die('Error: -s and -n parameters must be set');
if (!isset($options['t']))
    $options['t'] = 'UTC';
$logs = explode(',', $options['s']);
$name = $options['n'];

require_once(__DIR__ . '/vendor/autoload.php');
use DenisBeliaev\logAnalyzer\U;

$pathToDb = __DIR__ . '/store/' . $name . '.sqlite3';
if (!file_exists($pathToDb))
    copy(__DIR__ . '/store/template.db', $pathToDb);

foreach ($logs as $pathToLog)
    U::saveLog($pathToDb, $pathToLog, $options['t']);