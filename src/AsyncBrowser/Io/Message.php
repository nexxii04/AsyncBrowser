<?php


declare(strict_types = 1);

namespace AsyncBrowser\Io;

final class Message {

	public static function encode(string $type, string $content) {
		return json_encode([
			'type' => $type,
			'content' => $content
		]); 
	}

	public static function decode(string $message): array {
		return json_decode($message, true);
	}

	public static function request($content) {
		return self::encode('request', $content);
	}


	public static function resolve($content): string {
		return self::encode('resolve', $content);
	}

	public static function reject($content): string {
		return self::encode('reject', $content);
	}
}