<?php
namespace Bricks\Http\Routing;

/**
 * Представляет HTTP-ответ.
 *
 * Ответ будет передан клиенту в момент вызова метода send, а не в процессе его 
 * формирования. Это позволяет сформировать тело ответа прежде чем будут 
 * сформирован заголовок.
 *
 * @author Artur Sh. Mamedbekov
 */
class Response{
  /**
   * @var int Код ответа (на пример 200, 404, 500 и т.д.).
   */
  private $code;

  /**
   * @var array Параметры заголовка ответа.
   */
  private $header;

  /**
   * @var array Устанавливаемые ответом cookie.
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
   * Устанавливает код ответа.
   *
   * @param int $code Код ответа (на пример 200, 404, 500 и т.д.).
   */
  public function code($code){
    $this->code = $code;
  }

  /**
   * Устанавливает параметр заголовка ответа.
   *
   * @param string $param Имя целевого параметра.
   * @param string $value Устанавливаемое значение.
   */
  public function header($param, $value){
    $this->header[$param] = $value;
  }

  /**
   * Перенаправляет клиента.
   *
   * @param string $url Целевой URL.
   */
  public function redirect($url){
    $this->header('Location', $url);
  }

  /**
   * Устанавливает параметр cookie.
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
   * Добавляет значение в конец тела ответа.
   *
   * @param string $value Добавляемая строка.
   */
  public function body($value){
    $this->body .= $value;
  }

  /**
   * Добавляет данных в конец тела ответа предварительно сериализуя их как JSON 
   * строку.
   *
   * @param mixed $value Добавляемые данные.
   */
  public function bodyJson($value){
    $this->body(json_encode($value));
  }

  /**
   * Передает ответ клиенту.
   *
   * @warning Вызов этого метода передает все параметры ответа в Web-сервер.  
   * Рекомендуется не передавать других параметров после его вызова.
   */
  public function send(){
    http_response_code($this->code);

    foreach($this->header as $param => $value){
      header($param . ': ' . $value);
    }

    foreach($this->cookie as $param => $options){
      setcookie($param, $options['value'], $options['time']);
    }

    file_put_contents('php://output', $this->body);
  }
}
