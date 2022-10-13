<?php


declare(strict_types = 1);

namespace AsyncBrowser;

use AsyncBrowser\Exception\HeaderNotFound;

class Response {

    private $body;

    public function __construct(
        private int $statusCode,
        private array $headers,
        $body
    ) {
        $this->body = $body;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getBody(): string {
        return $this->body;
    }

    public function hasHeader($name): bool {
        return isset($this->headers[$name]);
    }

    public function getHeader($name) {
        if (!$this->hasHeader($name)) {
            throw new HeaderNotFound("header '" . $name . "' not found");
        }

        return $this->headers[$name];
    }
}