# Ответ

Для формирования и передачи HTTP-ответа клиенту используется класс _Response_.

Экземпляр этого класса накапливает параметры и данные ответа с помощью методов 
_code_, _header_, _cookie_, _body_ и т.д., а затем передает их Web-серверу при 
вызове метода _send_.

```php
$response = new Response;
$response->code(200);
$response->header('Content-Type', 'text/html');
$response->body('<div>Hello world</div>');
$response->send();
```
