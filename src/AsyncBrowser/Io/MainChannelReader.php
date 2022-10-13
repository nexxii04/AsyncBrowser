<?php

declare(strict_types = 1);

namespace AsyncBrowser\Io;

use Threaded;

final class MainChannelReader {

	public function __construct(private Threaded $buffer) {}

	public function read() {
		return $this->buffer->shift();
	}
}