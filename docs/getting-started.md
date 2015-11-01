# Введение

Данный пакет служит для организации роутинга HTTP-запросов. Маршрутизация 
ориентирована на URI путь целевого ресурса, на пример:

```php
use Bricks\Http\Routing\Request;
use Bricks\Http\Routing\Response;
use Bricks\Http\Routing\Router;

$router = new Router;
$router->get('/tasks/delete/([0-9]+)', 'method', 'Controller');

$response = new Response;
$router->run(new Request, $response)
$response->send();
```

Этому маршруту может соответствовать следующий вызов:

```
http://site.com/tasks/delete/1
```
