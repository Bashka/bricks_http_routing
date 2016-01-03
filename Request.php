<?php
namespace Bricks\Http\Routing;

/**
 * Представляет HTTP-запрос.
 *
 * @warning Класс получает данные с помощью глобальных массивов $_SERVER и 
 * $_COOKIE, что может вызвать ошибку в случае использования класса без 
 * Web-сервера.
 *
 * @author Artur Sh. Mamedbekov
 */
class Request{
  /**
   * @var string Метод запроса (GET, POST, PUT, DELETE и т.д.).
   */
  private $method;

  /**
   * @var string URI адрес целевого ресурса запроса.
   */
  private $path;

  /**
   * @var array Ассоциативный массив параметров заголовка запроса.
   */
  private $headers;

  /**
   * @var array Ассоциативный массив cookies, переданных в запросе.
   */
  private $cookies;

  /**
   * @var string Параметры запроса в виде строки.
   */
  private $input;

  /**
   * @var array Ассоциативный массив параметров запроса. Данное свойство 
   * заполняется при первой попытке доступа к параметрам запроса с помощью 
   * метода param.
   */
  private $parameters;

  public function __construct(){
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->path = $_SERVER['REQUEST_URI'];

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

    if($this->method() == 'GET'){
      $this->input = $_SERVER['QUERY_STRING'];
    }
    else{
      $this->input = file_get_contents('php://input');
    }
  }

  /**
   * Получает информацию о методе запроса.
   *
   * @return string Метод запроса (GET, POST, PUT, DELETE и т.д.).
   */
  public function method(){
    return $this->method;
  }

  /**
   * Получает URI адрес целевого ресурса запроса.
   *
   * @return string URI адрес целевого ресурса запроса.
   */
  public function path(){
    return $this->path;
  }

  /**
   * Получает значения параметра заголовка запроса.
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
   * Получает значения указанного параметра cookie.
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
   * Получает параметры запроса.
   *
   * @return string Параметры запроса в виде нераспарсиной строки (на пример 
   * param1=test&param2=123).
   */
  public function input(){
    return $this->input;
  }

  /**
   * Получает значение параметра запроса.
   *
   * @warning Метод приминим только в том случае, если строка параметров запроса 
   * соответствует формату сериализации параметров запроса (на пример 
   * param1=test&param2=123). В противном случае параметры не будут найдены.
   *
   * @param string $name [optional] Имя целевого параметра. Если параметр не 
   * задан, будут возвращены все параметры запроса в виде массива.
   *
   * @return string|array|null Значение параметра запроса. Если целевой параметр 
   * не указан, будут возвращены все параметры в виде массива. Если целевой 
   * параметр отсутствует, будет возвращен - null.
   */
  public function param($name = null){
    if(is_null($this->parameters)){
      $this->parameters = [];
      parse_str($this->input(), $this->parameters);
    }

    if(is_null($name)){
      return $this->parameters;
    }

    if(!isset($this->parameters[$name])){
      return null;
    }

    return $this->parameters[$name];
  }

  /**
   * Парсит параметры запроса как JSON-строку.
   *
   * @return stdClass Объект, восстановленный из строки параметров.
   */
  public function paramJson(){
    return json_decode($this->input());
  }
}
