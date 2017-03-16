<?php
/**
 *@author Tsvetan Tsvetanov <tsvetanov.19@gmail.com>
 *
 * Simple script that transforms routes from Epiphany ini file to FastRoute in Lumen
 * TODO: Add feature parse different routes and group them to $app->group
 *
 */
$ini_file = '/var/www/html/routes_api.ini';

$handle = fopen($ini_file, "r");

if ($handle) {
    $methods_total = 0;
    $method_string = '';
    $path = '';
    $action = '';
    $class = '';
    $function = '';
    while (($line = fgets($handle)) !== false) {
        $class_temp = $class;
        $complete = false;
        if (preg_match('/(\[)(.*)(POST|GET|PUT|DELETE)/', $line, $match)) {
            $path = str_replace(['<', '>'], ['{', '}'], $match[2]);
            $path = trim($path);
        } else if (preg_match('/(method\s+=\s+\")(POST|GET|PUT|DELETE)(?=\")/', $line, $match)) {
            $action = strtolower($match[2]);
        } else if (preg_match('/(class\s+=\s+\")(\w+)(?=\")/', $line, $match)) {
            $class = $match[2];
        } else if (preg_match('/(function\s+=\s+\")(\w+)(?=\")/', $line, $match)) {
            $methods_total++;
            $function = $match[2];
            $complete = true;
        }
	//Print FastRoute-s 
        if ($complete) {
            echo "<xmp>" . '$app->' . $action . "('" . $path . "','" . $class . "@" . $function . "');" . "</xmp>";
        }

        if(($class_temp !=$class)) {
            echo '<br>';
        }
    }
    fclose($handle);
    echo "<br>$methods_total<br>";
} else {
    echo 'cannot open file!';
}
