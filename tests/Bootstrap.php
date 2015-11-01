<?php
namespace Bricks\Http\Routing;

function getallheaders(){
  return ['Content-Type' => 'text/html'];
}

function file_get_contents($file){
  return 'param1=123&param2=test';
}

/**
 * @var ResponseTest_Mock Данная переменная инициализируется в тесте 
 * ResponseTest и используется для тестирования вызовов глобальных функций, 
 * перечисленных ниже. Вызов глобальных функций передается в одноименные методы 
 * этого объекта, которые фиксируются в тесте как вызовы методов Mock объекта.
 */
$responseMockGlobalFunctions;

function http_response_code($code){
  global $responseMockGlobalFunctions;
  $responseMockGlobalFunctions->http_response_code($code);
}

function header($string){
  global $responseMockGlobalFunctions;
  $responseMockGlobalFunctions->header($string);
}

function setcookie($name, $value, $time = 100){
  global $responseMockGlobalFunctions;
  $responseMockGlobalFunctions->setcookie($name, $value, $time);
}

function file_put_contents($file, $value){
  global $responseMockGlobalFunctions;
  $responseMockGlobalFunctions->file_put_contents($file, $value);
}
