<?php

declare(strict_types = 1);

namespace AsyncBrowser\Io;

use Threaded;

final class MainChannelWriter {

	public function __construct(private Threaded $buffer) {}

	public function write(string $string) {
		$this->buffer[] = $string;
	}
}