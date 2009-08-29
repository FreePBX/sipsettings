<?php
/* $Id:$ */

// Original Release by Philippe Lindheimer
// Copyright Philippe Lindheimer (2009)
// Copyright Bandwidth.com (2009)
/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	/* For testing:
	if (! @include_once("common/json.inc.php")) {
		include_once("/var/www/html/admin/common/json.inc.php");
	}
	*/
	include_once("common/json.inc.php");

	global $amp_conf;
	$output = array();
	$fn = "http://mirror.freepbx.org/whatismyip.php";

	$json_array['status'] = _('Failed to auto-detect settings');
	$json_array['externip'] = '';

	/* Fetch the IP address of this system, expects xml formatted as:
	<xml>
	  <ipaddress>
			nnn.nnn.nnn.nnn
	  </ipaddress>
	</xml>
	*/

	if (!$amp_conf['MODULEADMINWGET']) {
		$ip_xml = @file_get_contents($fn);
	}
	if (empty($ip_xml)) {
		exec("wget -O - '$fn' 2> /dev/null", $data_arr, $retcode);
		$ip_xml = implode("\n",$data_arr);
	}

	preg_match('|^<xml><ipaddress>(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})</ipaddress></xml>$|',$ip_xml,$matches);
	if (isset($matches[1])) {
		$json_array['externip'] = $matches[1];
		$json_array['status'] = _('Failed to auto-detect local network settings');

		// TODO: path to route?
		//
		exec('route -nv',$output,$retcode);
		foreach ($output as $line) {
			preg_match('/^\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s*(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/',$line,$matches);
			if (isset($matches[3]) && $matches[2] == '0.0.0.0' && substr($matches[1],0,4) != '169.') {
				$localnet[$matches[1]] = $matches[3];
				$json_array['status'] = 'success';
			}
		}
	} else {
		$json_array['status'] = _('Failed to auto-detect settings');
	}
	$json_array['localnet'] = $localnet;

	$json = new Services_JSON();
	sleep(0);
	header("Content-type: application/json"); 
	echo $json->encode($json_array);
