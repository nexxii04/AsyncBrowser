<?php

declare(strict_types = 1);

namespace AsyncBrowser\Io;

use AsyncBrowser\Request\Request;
use AsyncBrowser\Request\Result;

use Exception;

final class Buffer {

	public static function readRequest(string $buffer, &$id): Request {
		// requestId + request
		extract(json_decode($buffer, true));
		$id = $requestId;

		return new Request(
			$request['method'],
			$request['url'],
			$request['headers'],
			$request['body'],
			$request['timeout']
		);
	}

	public static function writeRequest($id, Request $request): string {
		return json_encode([
			'requestId' => $id,
			'request' => $request->toArray()
		]);
	}

	public static function readResult(string $buffer, &$id): Result {
		// requestId + result
		extract(json_decode($buffer, true));
		$id = $requestId;

		return new Result(
			$result['status-code'],
			$result['headers'],
			$result['body']
		);
	}

	public static function writeResult($id, Result $result): string {
		return json_encode([
			'requestId' => $id,
			'result' => $result->toArray()
		]);
	}

	public static function readException($buffer, &$id): Exception {
		// requestId + exception
		extract(json_decode($buffer, true));
		$id = $requestId;

		return new Exception($exception['message'], $exception['code']);
	}

	public static function writeException($id, Exception $exception): string {
		return json_encode([
			'requestId' => $id,
			'exception' => [
				'message' => $exception->getMessage(),
				'code' => $exception->getCode()
			]
		]);
	}
}