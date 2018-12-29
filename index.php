<?php
require_once 'variables.php';

define('DECODE_FILE_PATH', './decode_file.php');
define('ENCODE_FILE_PATH', './encode_file.php');

define('REPEATER_COUNT', 2);

define('GLOBAL_VARIABLES', [
    'Массив_Значений',
    'Массич_Значений_2',
]);

define('GLOBAL_FUNCTIONS', [
    'Функция'
]);

class Decode {

    private $file;

    public $console_output = '';

    function __construct() {
        $this->init();
    }

    private function console_log($str)
    {
        $this->console_output .= $str . "\n";
    }

    public function init()
    {
        !defined('DECODE_FILE_PATH') ? die('Не задана константа "DECODE_FILE_PATH"') : null;

        !file_exists( DECODE_FILE_PATH ) ? die('Файл "DECODE_FILE_PATH" не найден') : null;

        $this->file = file_get_contents( DECODE_FILE_PATH );

        $this->edit_variables_array('GLOBAL_VARIABLES');

        $this->edit_variables_function('GLOBAL_FUNCTIONS');

        $this->prepare();

        file_put_contents( ENCODE_FILE_PATH , $this->file);
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
                    return "'" . $value($matches[1]) . "'";
                }, $this->file);
            }
        }
    }

    private function prepare()
    {
        for ($i = 0; $i < REPEATER_COUNT; $i++) {
            $this->prepare_func();
        }

        $this->prepare_compute();
    }

    private function prepare_func()
    {
        $this->file = preg_replace_callback('/(min|round|strtoupper|strrev)\([^\(\)\$]+\)/', function($matches) {
            $result = eval("return $matches[0];");

            //$this->console_log(gettype($result) . ' - ' . $matches[0] . ' --- ' . eval("return $matches[0];"));

            switch (gettype($result)) {
                case 'string':
                    return "'" . $result . "'";
                    break;
                case 'double':
                    return $result;
                    break;
                case 'integer':
                    return $result;
                    break;
                default:
                    break;
            }
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
print_r($decode->console_output);
echo '</pre>';


