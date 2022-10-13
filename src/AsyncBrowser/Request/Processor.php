<?php


declare(strict_types = 1);

namespace AsyncBrowser\Request;

use AsyncBrowser\Io\Message;
use AsyncBrowser\Io\Buffer;
use AsyncBrowser\Io\ThreadChannelWriter;
use AsyncBrowser\Io\ThreadChannelReader;
use AsyncBrowser\Request\Method;
use AsyncBrowser\Request\Result;

use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\Promise\PromiseInterface;

use Psr\Http\Message\ResponseInterface;

class Processor {

	public function __construct(
		private ThreadChannelWriter $channelWriter,
		private ThreadChannelReader $channelReader
	) {}

	public function process(LoopInterface $loop): void {
		if (($buffer = $this->channelReader->read()) === null) {
			return;
		}

		// type + content
		extract(Message::decode($buffer));

		$request = Buffer::readRequest($content, $requestId);
		$this->browse($request, $loop)->then(
			function (ResponseInterface $response) use ($requestId) {
				// resolve
				$result = Buffer::writeResult(
					$requestId,
					$result = new Result(
						$response->getStatusCode(),
						$response->getHeaders(),
						(string)$response->getBody()
					)
				);

				$this->channelWriter->write(Message::resolve($result));
			},
			function (Exception $e) use ($requestId) {
				// reject
				$this->channelWriter->write(
					Message::reject(Buffer::writeException($requestId, $e))
				);
			}
		);
	}

	public function browse(Request $request, LoopInterface $loop): PromiseInterface {
		$browser = new Browser($loop);
		$promise = false;

		if ($request->getTimeout() !== false) {
			$browser->withTimeout($request->getTimeout());
		}

		if ($request->getMethod() === Method::GET) {
			$promise = $browser->get($request->getUrl(), $request->getHeaders());
		} else {
			$promise = $browser->request($request->getMethod(), $request->getUrl(), $request->getHeaders(), $request->getBody());
		}

		return $promise;
	}
}