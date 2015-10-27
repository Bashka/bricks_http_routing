<?php
namespace Bricks\Http\Routing;

/**
 * HTTP-ответ.
 *
 * Пример формирования ответа:
 *   $response = new Response;
 *   $response->header('Connection', 'close');
 *   $response->cookie('name', 'my name');
 *   $response->body('Hello world');
 *   $response->send();
 *
 * @author Artur Sh. Mamedbekov
 */
class Response{
  /**
   * @var int Код ответа.
   */
  private $code;

  /**
   * @var array Параметры заголовка ответа.
   */
  private $header;

  /**
   * @var array Устанавливаемые cookie.
   */
  private $cookie;

  /**
   * @var string Тело ответа.
   */
  private $body;

  public function __construct(){
    $this->code = 200;
    $this->header = [];
    $this->cookie = [];
    $this->body = '';
  }

  /**
   * Установка кода ответа.
   *
   * @param int $code Код ответа.
   */
  public function code($code){
    $this->code = $code;
  }

  /**
   * Установка параметра заголовка ответа.
   *
   * @param string $param Имя целевого параметра.
   * @param string $value Устанавливаемое значение.
   */
  public function header($param, $value){
    $this->header[$param] = $value;
  }

  /**
   * Выполнить перенаправление на указанную страницу.
   *
   * @param string $url Целевой URL.
   */
  public function redirect($url){
    $this->header('Location', $url);
  }

  /**
   * Установка параметра cookie.
   *
   * @param string $param Имя целевого параметра.
   * @param string $value Устанавливаемое значение. Если в качестве данного 
   * параметра передана пустая строка, cookie будет удалена.
   * @param int $time [optional] Время жизни cookie. По умолчанию cookie будет 
   * удалена после истечения сессии.
   */
  public function cookie($param, $value, $time = 0){
    if($value == ''){
      $time = time() - 300;
    }

    $this->cookie[$param] = ['value' => $value, 'time' => $time];
  }

  /**
   * Добавление значение в конец тела ответа.
   *
   * @param string $value Добавляемая строка.
   */
  public function body($value){
    $this->body .= $value;
  }

  /**
   * Добавление данных в виде JSON строки в конец тела ответа.
   *
   * @param mixed $value Добавляемые данные.
   */
  public function bodyJson($value){
    $this->body(json_encode($value));
  }

  /**
   * Передача ответа клиенту.
   */
  public function send(){
    http_response_code($this->code);

    foreach($this->header as $param => $value){
      header($param . ': ' . $value);
    }

    foreach($this->cookie as $param => $options){
      setcookie($param, $options['value'], $options['time']);
    }

    print($this->body);
  }
}
