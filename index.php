<?php
require_once 'variables.php';

define('DECODE_FILE_PATH', './decode_file.php');
define('ENCODE_FILE_PATH', './encode_file.php');

define('GLOBAL_VARIABLES', [
    'array1' => '_____352106240',
    'array2' => '____1938786290',
]);

define('GLOBAL_FUNCTIONS', [
    'func1' => '___42196359'
]);

class Decode {

    private $file;

    function __construct() {
        $this->init();
    }

    public function init()
    {
        !defined('DECODE_FILE_PATH') ? die('Не задана константа "DECODE_FILE_PATH"') : null;

        !file_exists(constant('DECODE_FILE_PATH')) ? die('Файл "DECODE_FILE_PATH" не найден') : null;

        $this->file = file_get_contents( DECODE_FILE_PATH );

        $this->edit_variables_array('GLOBAL_VARIABLES');

        $this->edit_variables_function('GLOBAL_FUNCTIONS');

        //$this->prepare();

        file_put_contents(constant('ENCODE_FILE_PATH'), $this->file);
    }

    private function edit_variables_array($const_name)
    {
        if (defined($const_name)) {
            foreach (constant($const_name) as $key => $value) {
                $this->file = preg_replace_callback('/\$GLOBALS\[\''.$value.'\'\]\[(\d+)\]/', function($matches) use ($value) {
                    return $GLOBALS[$value][$matches[1]];
                }, $this->file);
            }
        }
    }

    private function edit_variables_function($const_name)
    {
        if (defined($const_name)) {
            foreach (constant($const_name) as $key => $value) {
                $this->file = preg_replace_callback('/'.$value.'\((\d+)\)/', function($matches) use ($value) {
                    return "'".$value($matches[1])."'";
                }, $this->file);
            }
        }
    }

    private function prepare()
    {
        $this->prepare_round();

        $this->prepare_compute();
    }

    private function prepare_round()
    {
        $this->file = preg_replace_callback('/round\((.+?)\)/', function($matches) {
            return round($matches[1]);
        }, $this->file);
    }

    private function prepare_compute()
    {
        $this->file = preg_replace_callback('/\(([0-9-+*\/\s]{2,}?)\)/', function($matches) {
            return eval("return $matches[1];");
        }, $this->file);
    }


}

$decode = new Decode;





echo '<pre>';
print_r(0);
echo '</pre>';



