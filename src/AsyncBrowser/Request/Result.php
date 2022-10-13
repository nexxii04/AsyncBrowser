<?php

declare(strict_types = 1);

namespace AsyncBrowser\Request;

use AsyncBrowser\Response;

final class Result extends Response {

	public function toArray(): array {
		return [
			'status-code' => $this->getStatusCode(),
			'headers' => $this->getHeaders(),
			'body' => $this->getBody()
		];
	}
}