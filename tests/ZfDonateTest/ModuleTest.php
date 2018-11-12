<?php

namespace ZfDonateTest;

use ZfDonate\Module;

class ModuleTest extends \PHPUnit\Framework\TestCase {
	public function testLoadsConfig() {
		$module = new Module();
		$config = include __DIR__ . '/../../config/module.config.php';
		$this->assertSame($config,$module->getConfig());
	}
}
