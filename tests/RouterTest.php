<?php
namespace Bricks\Http\Routing;
require_once('Request.php');
require_once('Response.php');
require_once('RoutingException.php');
require_once('Router.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class RouterTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Router Роутер.
	 */
	private $router;

  /**
   * @var Request Представление запроса.
   */
  private $request;

  /**
   * @var Response Представление ответа.
   */
  private $response;

	public function setUp(){
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/tasks/delete/123';
    $this->request = new Request;

    $this->response = new Response;
    $this->router = new Router;
  }

  /**
   * Метод для тестирования вызова обработчика запроса.
   */
  public function callTest(Request $request, Response $response, array $match){
  }

  /**
   * Должен выполнять роутинг запроса.
   */
  public function testRun(){
    $testMock = $this->getMock(get_class($this));
    $testMock->expects($this->once())
      ->method('callTest')
      ->with($this->equalTo($this->request), $this->equalTo($this->response), $this->equalTo([0 => '123']));

    $this->router->get('~^/tasks/delete/([0-9]+)~', 'callTest', $testMock);
    $this->router->run($this->request, $this->response);
  }

  /**
   * Должен выполнять роутинг запроса с картой ALL.
   */
  public function testRun_shouldRouteAllRequests(){
    $testMock = $this->getMock(get_class($this));
    $testMock->expects($this->once())
      ->method('callTest')
      ->with($this->equalTo($this->request), $this->equalTo($this->response), $this->equalTo([0 => '123']));

    $this->router->all('~^/tasks/delete/([0-9]+)~', 'callTest', $testMock);
    $this->router->run($this->request, $this->response);
  }

  /**
   * Должен выбрасывать исключение если подходящего маршрута не найдено.
   */
  public function testRun_shouldThrowExceptionIfRouteNotFound(){
    $this->setExpectedException('Bricks\Http\Routing\RoutingException');

    $testMock = $this->getMock(get_class($this));
    $testMock->expects($this->never())
      ->method('callTest');

    $this->router->all('~^/tasks/get/([0-9]+)~', 'callTest', $testMock);
    $this->router->run($this->request, $this->response);
  }
}
