<?php


declare(strict_types = 1);

namespace AsyncBrowser\Request;

use AsyncBrowser\Response;

use Evenement\EventEmitter;

use Exception;

final class Request extends EventEmitter {

	private $timeout;

	// callables
	private $resolve;
	private $reject;

	public function __construct(
		private string $method,
		private string $url,
		private array $headers,
		private string $body,
		$timeout
	) {
		$this->timeout = is_numeric($timeout) ? $timeout : false;
		$this->on('resolve', function (Response $response) {
			$function = $this->resolve;
			if (!is_callable($function)) {
				return;
			}
			$function($response);
		});

		$this->on('reject',
			function (Exception $e) {
				$function = $this->reject;
				if (!is_callable($function)) {
					return;
				}
				$function($e);
			});
	}

	public function getUrl(): string {
		return $this->url;
	}

	public function getMethod(): string {
		return $this->method;
	}

	public function getHeaders(): array {
		return $this->headers;
	}

	public function getTimeout() {
		return $this->timeout;
	}

	public function getBody() {
		return $this->body;
	}

	public function onResolve(callable $resolve) {
		$this->resolve = $resolve;
	}

	public function onReject(callable $reject) {
		$this->reject = $reject;
	}

	public function toArray(): array {
		return [
			'method' => $this->method,
			'url' => $this->url,
			'headers' => $this->headers,
			'body' => $this->body,
			'timeout' => $this->timeout
		];
	}
}