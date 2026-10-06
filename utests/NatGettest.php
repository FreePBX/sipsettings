<?php
// vim: set ai ts=4 sw=4 ft=php:
class NatGetTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass() {
		if (!class_exists(\FreePBX\modules\Sipsettings\NatGet::class)) {
			include __DIR__."/../Natget.class.php";
		}
		if (!function_exists('fpbx_which')) {
			function fpbx_which($x) { return "/sbin/$x"; }
		}
	}

	public function testGetIP() {
		$nat = new FreePBX\modules\Sipsettings\NatGet();
		$result = $nat->getVisibleIP();
		$this->assertTrue(is_array($result));
		$this->assertArrayHasKey('status', $result);
		if ($result['status']) {
			$this->assertEquals(
				$result['address'],
				filter_var($result['address'], FILTER_VALIDATE_IP),
				"I wasn't returned a valid IP by getVisibleIP"
			);
		} else {
			$this->assertArrayHasKey('message', $result);
		}
	}

	public function testGetRoutes() {
		$nat = new FreePBX\modules\Sipsettings\NatGet();
		$routes = $nat->getRoutes();
		$this->assertTrue(is_array($routes), "Routes aren't an array? That's crazy");
		$this->assertFalse(empty($routes), "This machine doesn't have any extra routes");
		$cidr = $routes[0][1];
		if ($cidr != "24" && $cidr != "16" && $cidr != "8") {
			$this->fail("Is this route detection wrong? ".json_Encode($routes[0], JSON_THROW_ON_ERROR));
		}
	}

}


