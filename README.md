# AsyncBrowser
navegador asíncrono para enviar solicitudes HTTP en PoketMine-MP


**Tabla de contenidos**
* [Ejemplo inicial](#ejemplo-inicial)
* [API](#api)
	* [Browser](#browser)
		* [get()](#get)
		* [post()](#post)
		* [put()](#put)
		* [delete()](#delete)
		* [request()](#request)
		* [withTimeout()](#timeout)
		* [then()](#then)
	* [Response](#response)
		* [getStatusCode(()](#status)
		* [getHeaders()](#headers)
		* [getHeader()](#getheaders)
		* [hasHeader()](#hasheader)
		* [getBody()](#body)


## Ejemplo Inicial

El objeto ```Browser``` se usa para realizar peticiones y ```Response``` representa la respuesta de dicha petición.

use
```php
use AsyncBrowser\Browser;
use AsyncBrowser\Response;
```

ejemplo práctico
```php
$browser = new Browser();
$browser->request('GET', 'http://192.168.1.153:8080', [], '')
->then(
	function (Response $response) {
		// resolve
		echo "result " . $response->getBody();
	},
	function (Exception $e) {
		// reject
		echo $e->getMessage();
	}
)
->run();
```
es importante llamar el metodo ```run()```, de lo contrario la solicitud no sera enviada.
  
  
## API

### Browser
#### get
El método ```get(string $url, array $headers[]): Browser``` se puede utilizar para enviar una solicitud HTTP GET.

```php
$browser->get($url)->then(
	function (Response $response) {
		// resolve
	},
	function (Exception $e) {
		// reject
	}
)->run()
```

#### post
El método ```post(string $url, array $headers = [], string $body = ''): Browser``` se puede utilizar para enviar una solicitud HTTP POST.

```php
$browser->post(
	$url,
	['Content-type' => 'plain/text'],
	'hello world'
	)->then(
	function (Response $response) {
		// resolve
	},
	function (Exception $e) {
		// reject
	}
)->run()
```

#### put
El método ```put(string $url, array $headers = [], string $body = ''): Browser``` se puede utilizar para enviar una solicitud HTTP PUT.

```php
$browser->put(
	$url,
	['Content-type' => 'plain/text'],
	'hello world'
	)->then(
	function (Response $response) {
		// resolve
	},
	function (Exception $e) {
		// reject
	}
)->run()
```

#### update
El método ```update(string $url, array $headers = [], string $body = ''): Browser``` se puede utilizar para enviar una solicitud HTTP UPDATE.

```php
$browser->update(
	$url,
	['Content-type' => 'plain/text'],
	'hello world'
	)->then(
	function (Response $response) {
		// resolve
	},
	function (Exception $e) {
		// reject
	}
)->run()
```

#### delete
El método ```delete(string $url, array $headers = [], string $body = ''): Browser``` se puede utilizar para enviar una solicitud HTTP DELETE.

```php
$browser->delete(
	$url,
	['Content-type' => 'plain/text'],
	'hello world'
	)->then(
	function (Response $response) {
		// resolve
	},
	function (Exception $e) {
		// reject
	}
)->run()
```

#### request
El método ```request(string $method, string $url, array $headers = [], string $body = ''): Browser``` se puede utilizar para enviar una solicitud HTTP. Usted mismo tendra que definir el tipo de solicitud y sus parámetros.

```php
$browser->request(
	'POST',
	$url,
	['Content-type' => 'plain/text'],
	'hello world'
	)->then(
	function (Response $response) {
		// resolve
	},
	function (Exception $e) {
		// reject
	}
)->run()
```

#### timeout
El método ```withTimeout(int $time): Browser``` se puede utilizar para añadir un tiempo de espera. Por defecto las solicitudes son enviadas sin tiempo de espera.

```php

$browser = new Browser();
// 5sec
$browser->withTimeout(5)->get($url)->then(
	function (Response $response) {
		// resolve
	},
	function (Exception $e) {
		// reject
	}
)->run();
```

#### then
El método ```then(callable $onResolve, callable $onReject);``` se puede usar para añadir callables que se ejecutan una vez la solicitud allá sido un éxito o fracaso.
```php
$browser->get($url)->then(
	function (Response $response) {
		// called when the request is resolved
	},
	function (Exception $e) {
		// called en the request is rejected
	}
)->run();
```

### Response
El objetp ```Response``` representa la respuesta de la solicitud enviada.

#### status
El método ```getStatusCode(): int``` se puede usar para obtener el codigo de respuesta
```php
$browser->get($url)->then(
	function (Response $response) {
		echo 'status code: ' . $response->getStatusCode();
	},
	function (Exception $e) {
		
	}
)->run();
```

#### getheader
El método ```$response->getheader($name)``` se puede usar para obtener un elemento de la cabezera de la respuesta.
En caso de que no exista el elemento en la cabecera, arrojará un ```Exception```. use el método [hasHeader()](#hasheader) para comprobar si existe.
```php
$browser->get($url)->then(
	function (Response $response) {
		echo 'content type: ' . $response->getHeader('Content-type');
	},
	function (Exception $e) {
		
	}
)->run();
```

#### hasheader
El método ```$response->hasHeader($name): bool``` se puede usar para comprobar si existe un elemento dentro de la cabecera de la respuesta.
```php
$browser->get($url)->then(
	function (Response $response) {
		if ($response->hasHeader('token') {
			echo 'your token: ' . $response->getHeader('token');
		}
	},
	function (Exception $e) {
		
	}
)->run();
```

#### body
El método ```$response->getBody(): string``` se puede usar para obtener el cuerpo de la respuesta.
```php
$browser->get($url)->then(
	function (Response $response) {
		echo $response->getBody();
	},
	function (Exception $e) {
		
	}
)->run();
```

