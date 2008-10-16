<?php

set_include_path(dirname(dirname(dirname(dirname(__FILE__))))
                 . '/library/ServerLibraries/FirePHPCore/'
                 . '0.2'
                 . '/lib');

require_once('FirePHPCore/fb.php');

ob_start();

$firephp = FirePHP::getInstance(true);

$testing = new TestClass('Foo','Bar');

print_r($testing);


class TestClass
{
    public $text1;
    public $text2;
    public $output;
    private $trial1;
    private $trial2;
    private $trial3;
    
    function __construct($text1,$text2)
    {
        $this->text1 = $text1;
        $this->text2 = $text2;
        
        $this->get_trial1();
        
fb($this);        
    }
    
    private function get_trial1()
    {
        $this->trial1 = 'Trial 1 Text';
        $this->output = 'Hello World!';
    }

    function get_trial2()
    {
        $this->trial1 = 'Trial 2 Text';
    }

    function get_trial3()
    {
        $this->trial1 = array('Trial 3 Text','More Trial3 Text');
    }

}

