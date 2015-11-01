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

  public function __construct(){
    $this->map = [
      'GET' => [],
      'POST' => [],
      'PUT' => [],
      'DELETE' => [],
      'ALL' => [],
    ];
  }

  /**
   * Добавляет маршрут обработки запроса.
   *
   * @param string $method Целевой метод (GET, POST, PUT или DELETE).
   * @param string $pattern Регулярное выражение, которому должен отвечать URI 
   * целевого ресурса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URI пути, выделенных в шаблоне
   * @param object|string $context Контекст вызова обработчика в виде объекта 
   * или имени класса, который будет инстанциирован.
   */
  private function addRoute($method, $pattern, $callback, $context){
    array_push($this->map[$method], [
      'pattern' => $pattern,
      'callback' => $callback,
      'context' => $context
    ]);
  }

  /**
   * Вызывает указанную функцию в заданном контексте, передавая ей следующие 
   * параметры:
   *     - Request - представление запроса
   *     - Response - представление ответа
   *     - array - компоненты, выделенные в URI пути с помощью шаблона маршрута
   *
   * @param Request $request Представление запроса.
   * @param Response $response Представление ответа.
   * @param callable|string $callback Анонимная функция или имя целевой 
   * функции/метода.
   * @param string|object|null $context Контекст вызова в виде объекта или имени 
   * класса, который будет инстанциирован.
   * @param array $match Параметры, выделенные из пути с помощью шаблона 
   * шаршрутизатора.
   */
  private function call(Request $request, Response $response, $callback, $context, array $match){
    if(!is_null($context)){
      if(is_string($context)){
        $context = new $context;
      }
      $callback = [$context, $callback];
    }

    return call_user_func_array($callback, [$request, $response, $match]);
  }

  /**
   * Добавляет маршрут обработки GET запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URI 
   * путь ресурса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URI пути, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function get($pattern, $callback, $context = null){
    $this->addRoute('GET', $pattern, $callback, $context);
  }

  /**
   * Добавляет маршрут обработки POST запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URI 
   * путь ресурса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URI пути, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function post($pattern, $callback, $context = null){
    $this->addRoute('POST', $pattern, $callback, $context);
  }

  /**
   * Добавляет маршрут обработки PUT запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URI 
   * путь ресурса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URI пути, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function put($pattern, $callback, $context = null){
    $this->addRoute('PUT', $pattern, $callback, $context);
  }

  /**
   * Добавляет маршрут обработки DELETE запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URI 
   * путь ресурса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URI пути, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function delete($pattern, $callback, $context = null){
    $this->addRoute('DELETE', $pattern, $callback, $context);
  }

  /**
   * Добавляет маршрут обработки любых запросов. Данный маршрут обрабатывается 
   * только если не найдены подходящие обработчики GET, POST, PUT и DELETE 
   * запросов.
   *
   * @param string $pattern Регулярное выражение, которому должен отвечать URI 
   * путь ресурса для применения данного маршрута (вызова обработчика).
   * @param callable|string $callback Обработчик запроса в виде анонимной 
   * функции или имени функции.
   * При вызове обработчику будет переданно три параметра:
   *   - Экземпляр класса Request
   *   - Экземпляр класса Response
   *   - Массив компонентов URI пути, выделенных в шаблоне
   * @param object|string $context [optional] Контекст вызова обработчика в виде 
   * объекта или имени класса, который будет инстанциирован.
   */
  public function all($pattern, $callback, $context = null){
    $this->addRoute('ALL', $pattern, $callback, $context);
  }

  /**
   * Выполняет поиск и вызов подходящего обработчика запроса.
   *
   * @warning После выполнения обработчика следует передать полученный 
   * HTTP-ответ Web-серверу с помощью вызова метода send у экземпляра класса 
   * Response.
   *
   * @param Request $request Представление запроса. Объект будет передан в 
   * обработчик в качестве первого параметра.
   * @param Response $response Представление ответа. Объект будет передан в 
   * обработчик в качестве второго параметра.
   *
   * @throws RoutingException Выбрасывается в случае отсутствия подходящего 
   * обработчика запроса.
   *
   * @return mixed Данные, возвращаемые используемым обработчиком.
   */
  public function run(Request $request, Response $response){
    $urlPath = $request->path();

    foreach($this->map[$request->method()] as $options){
      $match = [];
      if(preg_match($options['pattern'], $urlPath, $match)){
        array_shift($match);
        return $this->call($request, $response, $options['callback'], $options['context'], $match);
      }
    }

    foreach($this->map['ALL'] as $options){
      $match = [];
      if(preg_match($options['pattern'], $urlPath, $match)){
        array_shift($match);
        return $this->call($request, $response, $options['callback'], $options['context'], $match);
      }
    }

    throw new RoutingException('Invalid path');
  }
}
