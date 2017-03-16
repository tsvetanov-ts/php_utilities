<?php

countFunctionLines();
/**
 * @author Tsvetan Tsvetanov <tsvetanov.19@gmail.com>
 * @param $path full path to the php project directory
 * This function counts the lines of all functions by file/class,
 * maximum nested {} brackets and outputs them
 * Only works with php files!
 */

function countFunctionLines()
{
    $api_dir = '/home/ttsvetanov/dev/cloud_faces/repo/cf_api/v1';
    $all_files = getDirContents($api_dir);
    $php_files = [];

    for ($k = 0; $k < count($all_files); $k++) {
        if (preg_match('/php$/', $all_files[$k])) {
            $php_files[] = $all_files[$k];
        }
    }

    echo "Function name, lines, layers<br>";

    for ($j = 0; $j < count($php_files); $j++) {
        $filename = $php_files[$j];
        if (strstr($filename, 'config')) {
            continue;
        }
        $handle = fopen($filename, "r");
        $brackets = 0;
        $name = basename($filename, '.php');
        $function_name = '';
        $max_brackets = 0;
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (preg_match('/(function)(\s)+(\w+)\(/', $line, $match)) {
                    if ($function_name != '') {
                        echo "<strong>!!!Failed to process  : $function_name @ $line<br></strong>";
                    }
                    $function_name = $name . '::' . $match[3];
                    $brackets = 0;
                    $max_brackets = 0;
                    $lines = 0;
                }
                if ($function_name != '') {
                    $lines++;
                    for ($c = 0; $c < strlen($line); $c++) {
                        $char = substr($line, $c, 1);

                        if ($char == '{') {
                            $brackets++;
                        }
                        if ($char == '}') {
                            $brackets--;
                            if ($brackets == 0) {
                                echo "$function_name, $lines, $max_brackets<br>";
                                $function_name = '';
                            }
                        }
                        if ($brackets > $max_brackets) {
                            $max_brackets = $brackets;
                        }
                    }
                }
            }

            fclose($handle);

        } else {
            // error opening the file.
            echo 'Cannot open ' . $filename;
        }

        echo '<br>';
        echo '<br>';
    }
}

function getDirContents($dir, &$results = array())
{
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}
