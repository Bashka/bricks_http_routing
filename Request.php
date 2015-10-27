<?php
namespace Bricks\Http\Routing;

/**
 * HTTP-запрос.
 *
 * @author Artur Sh. Mamedbekov
 */
class Request{
  /**
   * @var string Метод запроса.
   */
  private $method;

  /**
   * @var array Массив параметров заголовка запроса.
   */
  private $headers;

  /**
   * @var array Массив cookies.
   */
  private $cookies;

  /**
   * @var array Массив параметров запроса.
   */
  private $parameters;

  /**
   * @var string Входные параметры запроса.
   */
  private $input;

  public function __construct(){
    $this->method = $_SERVER['REQUEST_METHOD'];

    if(!function_exists('getallheaders')){
      $this->headers = []; 
      foreach ($_SERVER as $name => $value){ 
        if (substr($name, 0, 5) == 'HTTP_'){ 
          $this->headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
        } 
      } 
    }
    else{
      $this->headers = getallheaders();
    }

    $this->cookies = $_COOKIE;

    $this->input = file_get_contents('php://input');
    $this->parameters = [];
    parse_str($this->input, $this->parameters);
  }

  /**
   * Получение информации о методе запроса.
   *
   * @return array Информация о методе запроса в виде массива следующей 
   * структуры:
   *   [
   *     'method' => метод,
   *     'url' => URL-запроса,
   *     'protocol' => протокол
   *   ]
   */
  public function method(){
    return $this->method;
  }

  /**
   * Получение значения параметра заголовка запроса.
   *
   * @param string $param [optional] Имя целевого параметра. Если параметр не 
   * задан, будут возвращены все параметры заголовка запроса в виде массива.
   *
   * @return string|array|null Значение параметра заголовка запроса. Если 
   * целевой параметр не указан, будут возвращены все параметры заголовка 
   * запроса в виде массива. Если целевой параметр отсутствует в запросе, будет 
   * возвращен - null.
   */
  public function header($param = null){
    if(is_null($param)){
      return $this->headers;
    }

    if(!isset($this->headers[$param])){
      return null;
    }

    return $this->headers[$param];
  }

  /**
   * Получение значения указанного параметра cookie.
   *
   * @param string $param [optional] Имя целевого параметра cookie. Если 
   * параметр не задан, будут возвращены все параметры cookie в виде массива.
   *
   * @return string|array|null Значение параметра cookie. Если целевой параметр 
   * не указан, будут возвращены все параметры cookie в виде массива. Если 
   * целевой параметр отсутствует в cookie, будет возвращен - null.
   */
  public function cookie($param = null){
    if(is_null($param)){
      return $this->cookies;
    }

    if(!isset($this->cookies[$param])){
      return null;
    }

    return $this->cookies[$param];
  }

  /**
   * Получить входные параметры запроса.
   *
   * @return string Входные параметры запроса.
   */
  public function input(){
    return $this->input;
  }

  /**
   * Получение параметра запроса.
   *
   * @param string $name [optional] Имя целевого параметра. Если параметр не 
   * задан, будут возвращены все параметры запроса в виде массива.
   *
   * @return string|array|null Значение параметра запроса. Если целевой параметр 
   * не указан, будут возвращены все параметры в виде массива. Если целевой 
   * параметр отсутствует, будет возвращен - null.
   */
  public function param($name = null){
    if(is_null($name)){
      return $this->parameters;
    }

    if(!isset($this->parameters[$name])){
      return null;
    }

    return $this->parameters[$name];
  }

  /**
   * Парсинг параметров как JSON-строки.
   *
   * @return stdClass Объект, восстановленный из строки параметров.
   */
  public function paramJson(){
    return json_decode($this->input());
  }
}
