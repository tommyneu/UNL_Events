<?php
namespace UNL\UCBCN\APIv2;

class Home implements ModelInterface
{
    public $options = array();

    public function __construct($options = array()) 
    {
        $this->options = $options + $this->options;
    }

    public function run(string $method, array $data): array
    {
        return array('data' => 'Hello World');
    }
}