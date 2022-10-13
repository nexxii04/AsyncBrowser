<?php

declare(strict_types = 1);

namespace AsyncBrowser\Request;

use AsyncBrowser\Request\Processor;
use AsyncBrowser\IO\ThreadChannelWriter;
use AsyncBrowser\IO\ThreadChannelReader;

use React\Http\Browser;
use React\EventLoop\Loop;

use pocketmine\thread\Thread;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\snooze\SleeperHandler;

use Threaded;

final class RequestThread extends Thread {

	public function __construct(
		private SleeperNotifier $notifier,
		private Threaded $mainToThread,
		private Threaded $threadToMain
	) {}

	public function onRun(): void {
		try {
			require(dirname(__DIR__, 3) . '/vendor/autoload.php');
			$loop = Loop::get();
			$processor = new Processor(
				new ThreadChannelWriter($this->notifier, $this->threadToMain),
				new ThreadChannelReader($this->mainToThread)
			);
			$loop->addPeriodicTimer(0.001, function () use ($processor, $loop) {
				$processor->process($loop);
			});
			$loop->run();
		} catch (Exception $e) {
			throw $e;
		}
	}
}