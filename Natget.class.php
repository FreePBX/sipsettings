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
		foreach(["http://myip.freepbx.org:5060","http://myip.freepbx.org"] as $url) {
			try {
				$pest = new \PestXML($url);
				$pest->curl_opts[CURLOPT_TIMEOUT] = 4;
				$pest->curl_opts[CURLOPT_CONNECTTIMEOUT] = 1;
				$thing = $pest->get('/whatismyip.php');
				break;
			} catch(\Exception $e) {
				$thing = ["status" => false, "message" => $e->getMessage()];
			}
		}

		if(is_array($thing)) {
			return $thing;
		}

		if(!empty($thing->ipaddress) && filter_var((string)$thing->ipaddress, FILTER_VALIDATE_IP)) {
			return ["status" => true, "address" => (string)$thing->ipaddress];
		} else {
			return ["status" => false, "message" => _("Unknown Error")];
		}
	}

	/**
	 * Get Local routes
	 * @return array Array of routes
	 */
	public function getRoutes() {
		// Return a list of routes the machine knows about.
		$route = fpbx_which('route');
		if(empty($route)) {
			return [];
		}
		exec("$route -nv",$output,$retcode);
		if($retcode != 0 || empty($output)) {
			return [];
		}
		// Drop the first two lines, which are just headers..
		array_shift($output);
		array_shift($output);
		// Now loop through whatever's left
		$routes = [];
		foreach ($output as $line) {
			$arr = preg_split('/\s+/', $line);
			if((is_countable($arr) ? count($arr) : 0) < 3) {
				//some strange value we dont understand
				continue;
			}
			if ($arr[2] == "0.0.0.0" || $arr[2] == "255.255.255.255") {
				// Don't care about default or host routes
				continue;
			}
			if (str_starts_with($arr[0], "169.254")) {
				// Ignore ipv4 link-local addresses. See RFC3927
				continue;
			}
			$cidr = 32-log((ip2long($arr[2])^4_294_967_295)+1,2);
			$routes[] = [$arr[0], $cidr];
		}
		return $routes;
	}
	
	/**
  * setConfigurations
  *
  * @return void
  */
 public function setConfigurations(mixed $key,mixed $type,mixed $freepbx){
		$existingConfig = $this->getConfigurations($type,$freepbx);
		if($type == "externip"){
			$nat  = $key[0];
		}
		elseif (!empty($existingConfig)) {
			array_push($existingConfig,$key[0]);
			$nat = array_values($existingConfig);
		} else {
			$nat = array_values($key);
		}
		$freepbx->sipsettings->setConfig($type,$nat);
	}
	
	/**
  * getConfigurations
  *
  * @return void
  */
 public function getConfigurations(mixed $type,mixed $freepbx){
		return $freepbx->sipsettings->getConfig($type);
	}
}
