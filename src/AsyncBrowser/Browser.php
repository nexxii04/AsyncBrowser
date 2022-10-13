<?php

declare(strict_types = 1);

namespace AsyncBrowser;

use AsyncBrowser\Request\Queue;
use AsyncBrowser\Exception\BadRequest;
use AsyncBrowser\Request\Method;
use AsyncBrowser\Request\Request;

class Browser implements Method {

	private $method = '';
	private $url = '';
	private $headers = [];
	private $body = '';
	private $timeout = null;

	// callable function
	private $resolve = null;

	// callable function
	private $reject = null;

	public function __construct() {}

	public function get(string $url, array $headers = []): Browser {
		return $this->request(self::GET, $url, $headers);
	}

	public function post(string $url, array $headers = [], string $body = ''): Browser {
		return $this->request(self::POST, $url, $headers, $body);
	}

	public function put(string $url, array $headers, string $body): Browser {
		return $this->request(self::PUT, $url, $headers, $body);
	}

	public function update(string $url, array $headers, string $body): Browser {
		return $this->request(self::UPDATE, $url, $headers, $body);
	}

	public function delete(string $url, array $headers, string $body): Browser {
		return $this->request(self::UPDATE, $url, $headers, $body);
	}

	public function request(string $method, string $url, array $headers = [], string $body = ''): Browser {
		if (!self::exists($method)) {
			throw new BadRequest("invalid '" . $method . "' method");
		}
		$this->method = $method;
		$this->url = $url;
		$this->headers = $headers;
		$this->body = $body;

		return $this;
	}

	public function withTimeout(int $time): Browser {
		$this->timeout = $time;
		return $this;
	}

	public function then(callable $reslove, callable $reject): Browser {
		$this->resolve = $reslove;
		$this->reject = $reject;

		return $this;
	}

	public function run(): void {
		if ($this->timeout <= 0) {
			$this->timeout = false;
		}

		$request = new Request(
			$this->method,
			$this->url,
			$this->headers,
			$this->body,
			$this->timeout
		);
		$request->onResolve($this->resolve);
		$request->onReject($this->reject);
		$this->sendRequest($request);
	}

	private function sendRequest(Request $request) {
		Queue::getInstance()->pushRequest($request);
	}

	public static function exists(string $method): bool {
		switch ($method) {
			case self::GET:
				return true;
				break;

			case self::POST:
				return true;
				break;

			case self::PUT:
				return true;
				break;

			case self::UPDATE:
				return true;
				break;

			case self::DELETE:
				return true;
				break;
		}

		return false;
	}
}