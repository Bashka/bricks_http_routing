# Роутинг

Маршрутизация выполняется с помощью экземпляра класса _Router_, который 
позволяет зарегистрировать допустимые маршруты и обработать вызов.

## Добавление маршрута

Добавление маршрута осуществляется с помощью одного из следующих методов:

- `get` - маршрут обработки GET запроса
- `post` - маршрут обработки POST запроса
- `put` - маршрут обработки PUT запроса
- `delete` - маршрут обработки DELETE запроса
- `all` - маршрут обработки запроса любого типа. Данный маршрут используется 
  только в случае, если ни одни из предыдущих не подходит для обработки

Все перечисленные методы принимают в качестве первого параметра регулярное 
выражение, которому должен соответствовать URI путь ресурса запроса. Группы в 
этом регулярном выражении будут переданы в обработчик в качестве третьего 
параметра.

Методы так же должны получить в качестве второго параметра обработчик маршрута, 
которым может быть:

- Анонимная функция
- Строка, содержащая имя глобальной функции
- Строка содержащая имя метода

В последнем случае необхомо так же передать третий параметр, определяющий 
контекст вызова метода. Этим параметром может быть:

- Объект
- Имя класса, который будет инстанциирован перед вызовом

Пример объявления маршрута:

```php
use Bricks\Http\Routing\Router;

$router = new Router;
$router->get('/tasks/delete/([0-9]+)', 'method', 'Controller');
```

Этому маршруту может соответствовать следующий вызов:

```
http://site.com/tasks/delete/1
```

## Маршрутизация

Для выполнения маршрутизации используется метод _run_, который принимает 
экземпляры классов _Request_ и _Response_, которые передаются в качестве второго 
и третьего параметра в обработчик.  Данный метод возвращает те данные, которые 
возвращает использованный обработчик.

Пример выполнения маршрутизации:

```php
use Bricks\Http\Routing\Request;
use Bricks\Http\Routing\Response;
use Bricks\Http\Routing\Router;

$router = new Router;
$router->get(
  '/tasks/delete/([0-9]+)',
  function(Request $request, Response $response, $match){
    ...
  }
);
$router->run(new Request, new Response);
```

В случае, если подходящий обработчик не найден, метод выбрасывает исключение 
_RoutingException_.

Пример обработки исключения:

```php
use Bricks\Http\Routing\RoutingException;
use Bricks\Http\Routing\Router;

$router = new Router;
$router->get(...);
try{
  $router->run(...);
}
catch(RoutingException $e){
    // Обработка.
}
```