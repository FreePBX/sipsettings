<?php
// vim: set ai ts=4 sw=4 ft=php:
namespace FreePBX\modules\Sipsettings;
class NatGet {
	public $urls;

	public function __construct() {
		$this->urls = array(
			array("http://myip.freepbx.org:5060/whatismyip.php", "xml"),
		);
	}

	public function getVisibleIP() {
		$ip = false;
		foreach ($this->urls as $arr) {
			if ($arr[1] == "xml") {
  				$xml = file_get_contents($arr[0]);
				if (preg_match("/ress>(.+?)<\/ipaddr/", $xml, $out)) {
					$ip = $out[1];
				}
			} else {
				throw new \Exception("Only know about xml at the moment");
			}

			// Lets see if we found the IP address with this lookup.
			if ($ip) {
				if (filter_var($ip, FILTER_VALIDATE_IP)) {
					// Yay. We did.
					return $ip;
				}
			}
		}

		// We ran out of places to try. Return false.
		return false;
	}

	public function getRoutes() {
		// Return a list of routes the machine knows about.
		$route = fpbx_which('route');
		exec("$route -nv",$output,$retcode);
		// Drop the first two lines, which are just headers..
		array_shift($output);
		array_shift($output);
		// Now loop through whatever's left
		$routes = array();
		foreach ($output as $line) {
			$arr = preg_split('/\s+/', $line);
			if ($arr[2] == "0.0.0.0" || $arr[2] == "255.255.255.255") {
				// Don't care about default or host routes
				continue;
			}
			if (substr($arr[0], 0, 7) == "169.254") {
				// Ignore ipv4 link-local addresses. See RFC3927
				continue;
			}
			$cidr = 32-log((ip2long($arr[2])^4294967295)+1,2);
			$routes[] = array($arr[0], $cidr);
		}
		return $routes;
	}
}
