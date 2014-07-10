<?php

global $currentcomponent;

$currentcomponent->addguielem('_top', new gui_hidden('category', 'pjsip'));

$currentcomponent->addoptlistitem("yn", "yes", _("Yes"));
$currentcomponent->addoptlistitem("yn", "no", _("No"));
$currentcomponent->setoptlistopts("yn", "sort", false);

$allowhelp = _("When set Asterisk will allow Guest SIP calls and send them to the Default SIP context. Turning this off will keep anonymous SIP calls from entering the system. Doing such will also stop 'Allow Anonymous Inbound SIP Calls' from functioning. Allowing guest calls but rejecting the Anonymous SIP calls below will enable you to see the call attempts and debug incoming calls that may be mis-configured and appearing as guests.");

$currentcomponent->addguielem("_top", new gui_pageheading(null, _("Misc PJSip Settings"), false), 1);
$currentcomponent->addguielem("_top", new gui_radio("allowguest", $currentcomponent->getoptlist("yn"), $this->getConfig("allowguest"), _("Allow SIP Guests"), $allowhelp), 1);
$currentcomponent->addguielem("_top", new gui_radio("showadvanced", $currentcomponent->getoptlist("yn"), $this->getConfig("showadvanced"), _("Show Advanced Settings"), ''), 1);

$sa = $this->getConfig('showadvanced');

$interfaces['auto'] = array('0.0.0.0', 'All', '0');

if ($sa != "no") {
	// Discover all interfaces.

	exec("/sbin/ip -o addr", $result, $ret);
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
}


$currentcomponent->addguielem("", new gui_pageheading('_top', _("Transports"), false), 2);
$currentcomponent->addguielem("", new gui_label(null, "Note that the interface is only displayed for your information, and is not referenced by asterisk."));
$currentcomponent->addguielem("", new gui_label(null, "Also be warned: After you enable/disable a transport, asterisk needs to be <strong>restarted</strong>, not just reloaded."));

$protocols = $this->getConfig("protocols");

foreach ($protocols as $p) {
	$allBinds = $this->getConfig("binds");
	$binds = $allBinds[$p];
	foreach ($interfaces as $i) {
		// Skip interfaces without an IP address.
		if (empty($i))
			continue;
		// $i = array( "1.2.3.4", "eth1", "24");
		if ($p == "udp") {
			$priority = 2;
		} else {
			$priority = 3;
		}
		$currentcomponent->addguielem("$p Protocol", new gui_checkbox($p."bindip-".$i[0], isset($binds[$i[0]]), "$p - ${i[0]} - ${i[1]}"), $priority);
	}

	// Now display a section for each one.

	foreach ($binds as $ip => $stat) {
		if ($stat != "on")
			continue;
		$vars = array(
			$p."port-$ip" => _("Port to Listen On"),
			$p."domain-$ip" => _("Domain the transport comes from"),
			$p."extip-$ip" => _("External IP Address (used for NAT)"),
			$p."localnet-$ip" => array(_("Local network"), _("Local network is provided here to allow distinct local networks per interface.")),
		);

		foreach ($vars as $v => $t) {
			if (is_array($t)) {
				$currentcomponent->addguielem("$p - $ip", new gui_textbox($v, $this->getConfig($v), $t[0], $t[1]), $priority+2);
			} else {
				$currentcomponent->addguielem("$p - $ip", new gui_textbox($v, $this->getConfig($v), $t), $priority+2);
			}
		}
	}
}
