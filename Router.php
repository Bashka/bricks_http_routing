<?php
namespace Bricks\Http\Routing;
require_once(__DIR__ . '/Request.php');
require_once(__DIR__ . '/Response.php');
require_once(__DIR__ . '/RoutingException.php');

/**
 * Объекты класса маршрутизируют HTTP-запросы к функциям и методам обработчикам.
 *
 * Пример установки анонимной функции-обработчика:
 *   $router = new Router;
 *   $router->route('/todos', function($request, $response, $params){
 *    $response->body('Hello world');
 *   });
 *   $router->run();
 *
 * Пример установки метода класса:
 *   $router = new Router;
 *   $router->delete('/todos/([0-9]+)', 'deleteAction', 'TodoController');
 *   $router->run();
 *
 * @author Artur Sh. Mamedbekov
 */
class Router{
  /**
   * @var array Карта маршрутизации.
   */
  private $map;

  /**
   * @var Request Объект запроса.
   */
  private $request;

  /**
   * @var Response Объект ответа.
   */
  private $response;

  public function __construct(){
    $this->request = new Request;
    $this->response = new Response;
    $this->map = [
      'GET' => [],
      'POST' => [],
      'PUT' => [],
      'DELETE' => [],
      'ALL' => [],
    ];
  }

  /**
   * Добавление маршрута для обработки GET запросов.
   *
   * @param string $method Целевой метод.
   * @param string $pattern Регулярное выражение, которому должен отвечать URL 
   * запроса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URL, выделенных в шаблоне
   * @param object|string $context Контекст вызова обработчика в виде объекта 
   * или имени класса, который будет инстанциирован.
   */
  private function addRoute($method, $pattern, $callback, $context){
    array_push($this->map[$method], [
      'pattern' => '~^' . $pattern . '~',
      'callback' => $callback,
      'context' => $context
    ]);
  }

  /**
   * Метод вызывает указанную функцию в заданном контексте.
   *
   * @param callable|string $callback Анонимная функция или имя целевой 
   * функции/метода.
   * @param string|object|null $context Контекст вызова в виде объекта или имени 
   * класса, который будет инстанциирован.
   * @param array $match Параметры, выделенные из пути с помощью шаблона 
   * шаршрутизатора.
   */
  private function call($callback, $context, $match){
    if(!is_null($context)){
      if(is_string($context)){
        $context = new $context;
      }
      $callback = [$context, $callback];
    }

    $result = call_user_func_array($callback, [$this->request, $this->response, $match]);
    $this->response->send();
    return $result;
  }

  /**
   * Добавление маршрута для обработки GET запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URL 
   * запроса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URL, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function get($pattern, $callback, $context = null){
    $this->addRoute('GET', $pattern, $callback, $context);
  }

  /**
   * Добавление маршрута для обработки POST запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URL 
   * запроса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URL, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function post($pattern, $callback, $context = null){
    $this->addRoute('POST', $pattern, $callback, $context);
  }

  /**
   * Добавление маршрута для обработки PUT запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URL 
   * запроса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URL, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function put($pattern, $callback, $context = null){
    $this->addRoute('PUT', $pattern, $callback, $context);
  }

  /**
   * Добавление маршрута для обработки DELETE запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URL 
   * запроса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URL, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function delete($pattern, $callback, $context = null){
    $this->addRoute('DELETE', $pattern, $callback, $context);
  }

  /**
   * Добавление маршрута для обработки любых запросов. Данный маршрут 
   * обрабатывается только если не найдены подходящие обработчики GET, POST, PUT 
   * и DELETE запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URL 
   * запроса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URL, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function all($pattern, $callback, $context = null){
    $this->addRoute('ALL', $pattern, $callback, $context);
  }

  /**
   * Выполняет поиск и вызов подходящего обработчика запроса.
   *
   * Пример отсутствия обработчика маршрута:
   *   $router = new Router;
   *   $router->get('/todos', 'indexAction', 'TodoController');
   *   try{
   *     $router->run();
   *   }
   *   catch(RoutingException $e){
   *     $response = new Response;
   *     $response->code(404);
   *     $response->send();
   *   }
   *
   * @throws RoutingException Выбрасывается в случае отсутствия подходящего 
   * обработчика запроса.
   *
   * @return mixed Данные, возвращаемые используемым обработчиком.
   */
  public function run(){
    $urlPath = $_SERVER['REQUEST_URI'];

    foreach($this->map[$this->request->method()] as $options){
      $match = [];
      if(preg_match($options['pattern'], $urlPath, $match)){
        unset($match[0]);
        return $this->call($options['callback'], $options['context'], $match);
      }
    }

    foreach($this->map['ALL'] as $options){
      $match = [];
      if(preg_match($options['pattern'], $urlPath, $match)){
        unset($match[0]);
        return $this->call($options['callback'], $options['context'], $match);
      }
    }

    throw new RoutingException('Invalid path');
  }
}
