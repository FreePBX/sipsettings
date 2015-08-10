<?php
// vim: set ai ts=4 sw=4 ft=php:
namespace FreePBX\modules\Sipsettings;
class NatGet {

	/**
	 * Get Visible IP by querying freepbx.org
	 * @return array Status of result
	 */
	public function getVisibleIP() {
		$ip = false;
		try {
			$pest = new \PestXML("http://myip.freepbx.org:5060");
			$thing = $pest->get('/whatismyip.php');
		} catch(\Exception $e) {
			return array(
				"status" => false,
				"message" => $e->getMessage()
			);
		}
		if(!empty($thing->ipaddress) && filter_var((string)$thing->ipaddress, FILTER_VALIDATE_IP)) {
			return array(
				"status" => true,
				"address" => (string)$thing->ipaddress
			);
		} else {
			return array(
				"status" => false,
				"message" => _("Unknown Error")
			);
		}
	}

	/**
	 * Get Local routes
	 * @return array Array of routes
	 */
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
