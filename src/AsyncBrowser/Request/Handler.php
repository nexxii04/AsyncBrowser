<?php


declare(strict_types = 1);

namespace AsyncBrowser\Request;

use AsyncBrowser\Response;
use AsyncBrowser\Request\Queue;
use AsyncBrowser\Io\Message;
use AsyncBrowser\Io\Buffer;
use AsyncBrowser\Io\MainChannelReader;

final class Handler {

	public function __construct(
		private MainChannelReader $channelReader,
		private Queue $queue
	) {}

	public function handle(): bool {
		if (($buffer = $this->channelReader->read()) === null) {
			return false;
		}

		// type + $content
		extract(Message::decode($buffer));
		switch ($type) {
			case 'resolve':
				$this->resolve($content);
				break;

			case 'reject':
				$this->reject($content);
				break;
			//later more types
		}

		return true;
	}

	public function resolve(string $buffer): void {
		$result = Buffer::readResult($buffer, $requestId);
		$response = new Response(
			$result->getStatusCode(),
			$result->getHeaders(),
			$result->getBody()
		);

		$this->queue->emit('resolve', [(int)$requestId, $response]);
	}

	public function reject(string $buffer): void {
		$exception = Buffer::readException($buffer, $requestId);
		$this->queue->emit('reject', [(int)$requestId, $exception]);
	}
}