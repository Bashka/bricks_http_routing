<?php
namespace Bricks\Http\Routing;
require_once('Request.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class RequestTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Request Представление запроса.
	 */
	private $request;

	public function setUp(){
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/script.php';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $_SERVER['HTTP_CONTENT_TYPE'] = 'text/html';
    $_SERVER['QUERY_STRING'] = 'param1=123&param2=test';
    $_COOKIE = ['var' => 'test'];

    $this->request = new Request;
  }

  /**
   * Должен возвращать имя используемого метода запроса.
   */
  public function testMethod(){
    $this->assertEquals('GET', $this->request->method());
  }

  /**
   * Должен возвращать URI адрес целевого ресурса запроса.
   */
  public function testPath(){
    $this->assertEquals('/script.php', $this->request->path());
  }

  public function testIp(){
    $this->assertEquals('127.0.0.1', $this->request->ip());
  }

  /**
   * Должен возвращать значение параметра заголовка запроса.
   */
  public function testHeader(){
    $this->assertEquals('text/html', $this->request->header('Content-Type'));
  }

  /**
   * Должен возвращать массив параметров если не указан целевой.
   */
  public function testHeader_shouldReturnAllParamsIfNotSpecific(){
    $this->assertEquals(['Content-Type' => 'text/html'], $this->request->header());
  }

  /**
   * Должен возвращать null если параметр не определен.
   */
  public function testHeader_shouldNullReturnIfParamNotFound(){
    $this->assertNull($this->request->header('test'));
  }

  /**
   * Должен возвращать значение cookie.
   */
  public function testCookie(){
    $this->assertEquals('test', $this->request->cookie('var'));
  }

  /**
   * Должен возвращать все значения cookies, если не указан целевой.
   */
  public function testCookie_shouldReturnAllCookiesIfNotSpecific(){
    $this->assertEquals(['var' => 'test'], $this->request->cookie());
  }

  /**
   * Должен возвращать null если cookie не определен.
   */
  public function testCookie_shouldNullReturnIfCookieNotFound(){
    $this->assertNull($this->request->cookie('test'));
  }

  /**
   * Должен возвращать все параметры запроса в виде строки.
   */
  public function testInput(){
    $this->assertEquals('param1=123&param2=test', $this->request->input());
  }

  /**
   * Должен возвращать значение параметра запроса.
   */
  public function testParam(){
    $this->assertEquals('123', $this->request->param('param1'));
    $this->assertEquals('test', $this->request->param('param2'));
  }

  /**
   * Должен возвращать все параметры запроса если не указан целевой.
   */
  public function testParam_shouldReturnAllParamsIfNotSpecific(){
    $this->assertEquals(['param1' => '123', 'param2' => 'test'], $this->request->param());
  }

  /**
   * Должен возвращать null если параметр не найден.
   */
  public function testParam_shouldNullReturnIfParamNotFound(){
    $this->assertNull($this->request->param('test'));
  }
}
