<?php
namespace Bricks\Http\Routing;
require_once('Response.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class ResponseTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Response Представление ответа.
	 */
	private $response;

	public function setUp(){
    $this->response = new Response;
  }

  /**
   * Метод для тестирования вызова глобальной функции http_response_code.
   */
  public function http_response_code($code){
  }

  /**
   * Метод для тестирования вызова глобальной функции header.
   */
  public function header($string){
  }

  /**
   * Метод для тестирования вызова глобальной функции setcookie.
   */
  public function setcookie($name, $value, $time){
  }

  /**
   * Метод для тестирования вызова глобальной функции file_put_contents.
   */
  public function file_put_contents($file, $value){
  }

  /**
   * Должен передавать ответ Web-серверу.
   */
  public function testSend(){
    global $responseMockGlobalFunctions;
    $responseMockGlobalFunctions = $this->getMock(get_class($this));
    $responseMockGlobalFunctions->expects($this->once())
      ->method('http_response_code')
      ->with($this->equalTo(201));

    $responseMockGlobalFunctions->expects($this->at(1))
      ->method('header')
      ->with($this->equalTo('Content-Type: text/html'));

    $responseMockGlobalFunctions->expects($this->at(2))
      ->method('header')
      ->with($this->equalTo('Location: http://site.test'));

    $responseMockGlobalFunctions->expects($this->once())
      ->method('setcookie')
      ->with($this->equalTo('param'), $this->equalTo('val'), $this->equalTo(1));
    
    $responseMockGlobalFunctions->expects($this->once())
      ->method('file_put_contents')
      ->with($this->equalTo('php://output'), $this->equalTo('text'));

    $this->response->code(201);
    $this->response->header('Content-Type', 'text/html');
    $this->response->redirect('http://site.test');
    $this->response->cookie('param', 'val', 1);
    $this->response->body('text');
    $this->response->send();
  }
}
