<?php

global $currentcomponent;

$currentcomponent->addguielem('_top', new gui_pageheading('_top', _("Transports"), false), 0);
$currentcomponent->addguielem('_top', new gui_hidden('category', 'pjsip'));

// Discover all interfaces.
exec("/sbin/ip -o addr", $result, $ret);

if ($ret != 0)
	throw new Exception('ip -o addr failed somehow.');

foreach ($result as $line) {
	$vals = preg_split("/\s+/", $line);

	// We only care about ipv4 (inet) lines, or definition lines
	if ($vals[2] != "inet" && $vals[3] != "mtu")
		continue;

	if (preg_match("/(.+?)(?:@.+)?:$/", $vals[1], $res)) { // Matches vlans, which are eth0.100@eth0
		// It's a network definition.
		// This won't clobber an exsiting one, as it always comes
		// before the IP addresses.
		$interfaces[$res[1]] = array();
		continue;
	}
	if ($vals[4] == "scope" && $vals[5] == "host") {
		$int = 6;
	} else {
		$int = 8;
	}

	// Strip netmask off the end of the IP address
	$ret = preg_match("/(\d*+.\d*+.\d*+.\d*+)\/(\d*+)/", $vals[3], $ip);

	$interfaces[$vals[$int]] = array($ip[1], $vals[$int], $ip[2]);

}


$currentcomponent->addguielem("_top", new gui_label(null, "Note that the interface is only displayed for your information, and is not referenced by asterisk."));
$currentcomponent->addguielem("_top", new gui_label(null, "Also be warned: After you enable/disable a transport, asterisk needs to be <strong>restarted</strong>, not just reloaded."));

$protocols = $this->getConfig("protocols");

foreach ($protocols as $p) {
	$allBinds = $this->getConfig("binds");
	$binds = $allBinds[$p];
	foreach ($interfaces as $i) {
		// Skip interfaces without an IP address.
		if (empty($i))
			continue;
		// $i = array( "1.2.3.4", "eth1", "24");
		$currentcomponent->addguielem("$p Protocol", new gui_checkbox($p."bindip-".$i[0], isset($binds[$i[0]]), "$p - ${i[0]} - ${i[1]}", 'help'), 2);
	}

	// Now display a section for each one.

	foreach ($binds as $ip => $stat) {
		if ($stat != "on")
			continue;
		$vars = array(
			$p."port-$ip" => "Port to Listen On",
			$p."domain-$ip" => "Domain the transport comes from",
			$p."extip-$ip" => "External IP Address (used for NAT)",
		);

		foreach ($vars as $v => $t) {
			$currentcomponent->addguielem("$p - $ip", new gui_textbox($v, $this->getConfig($v), $t, "helptext"));
		}
	}
}

