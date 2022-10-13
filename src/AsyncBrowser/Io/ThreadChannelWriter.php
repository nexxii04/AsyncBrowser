<?php


declare(strict_types = 1);

namespace AsyncBrowser\Io;

use pocketmine\snooze\SleeperNotifier;
use Threaded;

final class ThreadChannelWriter {

	public function __construct(
		private SleeperNotifier $notifier,
		private Threaded $buffer
	) {}

	public function write(string $string) {
		$this->buffer[] = $string;
		$this->notifier->wakeupSleeper();
	}
}