<?php

declare(strict_types = 1);

namespace AsyncBrowser;

use AsyncBrowser\Request\Queue;
use AsyncBrowser\Exception\AsyncBrowserException;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {


	public function onLoad(): void {
		$bootstrap = dirname(__DIR__, 2) . '/vendor/autoload.php';
		if (!is_file($bootstrap)) {
			throw new AsyncBrowserException('install the composer dependencies');
		}

		require($bootstrap);
		new Queue();
	}
}