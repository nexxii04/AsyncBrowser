<?php

declare(strict_types = 1);

namespace AsyncBrowser\Request;

use AsyncBrowser\Response;
use AsyncBrowser\Io\Message;
use AsyncBrowser\Io\Buffer;
use AsyncBrowser\Io\MainChannelWriter;
use AsyncBrowser\Io\MainChannelReader;
use AsyncBrowser\Request\Request;
use AsyncBrowser\Request\RequestThread;

use Evenement\EventEmitter;

use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\snooze\SleeperNotifier;

use Threaded;
use Exception;
use PTHREADS_INHERIT_NONE;

class Queue extends EventEmitter {

	use SingletonTrait;

	private $thread;

	private $channel;
	private $threadNotifier;

	private $queue = [];

	public function __construct() {
		self::setInstance($this);
		$notifier = new SleeperNotifier();
		$mainToThead = new Threaded();
		$threadToMain = new Threaded();

		$this->thread = new RequestThread($notifier, $mainToThead, $threadToMain);
		$this->channel = new MainChannelWriter($mainToThead);

		// listener
		$this->on('resolve', function ($requestId, Response $response) {
			$request = clone $this->queue[$requestId];
			unset($this->queue[$requestId]);
			$request->emit('resolve', [$response]);
		});

		$this->on('reject', function ($requestId, Exception $e) {
			$request = clone $this->queue[$requestId];
			unset($this->queue[$requestId]);
			$request->emit('reject', [$e]);
		});

		// run
		$this->run($notifier, $threadToMain);
	}

	public function pushRequest(Request $request) {
		$id = spl_object_id($request);
		$this->queue[$id] = $request;
		$this->channel->write(Message::request(Buffer::writeRequest($id, $request)));
	}

	private function run(SleeperNotifier $notifier,
		Threaded $threadToMain): void {
		$handler = new Handler(new MainChannelReader ($threadToMain), $this);
		Server::getInstance()->getTickSleeper()->addNotifier($notifier,
			function () use ($handler) {
				while ($handler->handle());
			}
		);

		try {
			$this->thread->start(PTHREADS_INHERIT_NONE);
		} catch (Exception $e) {
			throw $e;
		}
	}
}