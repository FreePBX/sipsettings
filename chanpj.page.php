<?php
global $currentcomponent;
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
		$ret = preg_match("/(\d*+.\d*+.\d*+.\d*+)[\/(\d*+)]*/", $vals[3], $ip);

		$interfaces[$vals[$int]] = array($ip[1], $vals[$int], $ip[2]);
	}
}
$protocols = $this->getConfig("protocols");
$protohtml = $udphtml = $bindthml = '';
foreach ($protocols as $p) {
	$allBinds = $this->getConfig("binds");
	$binds = !empty($allBinds[$p]) && is_array($allBinds[$p]) ? $allBinds[$p] : array();
	$cbs = '';
	$lastproto="";
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
		$thisTitle = "$p - ${i[0]} - ${i[1]}";
		$thisID = $p."bindip-".$i[0];
		if($lastproto != $p){
			if($lastproto != ""){
				$cbs .= '</div>';
			}
			$cbs .= '
				<div class="section-title" data-for="pjs.'.$p.'"><h3>
					<i class="fa fa-minus"></i> '.$p.'</h3>
				</div>
				<div class="section" data-id="pjs.'.$p.'">
			';
		}
		$binds[$i[0]] = isset($binds[$i[0]])?$binds[$i[0]]:'off';
		$cbs .= '
		<!--'.$thisTitle.'-->
		<div class="element-container">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="form-group">
							<div class="col-md-3">
								<label class="control-label" for="'.$thisID.'">'. $thisTitle .'</label>
								<i class="fa fa-question-circle fpbx-help-icon" data-for="'.$thisID.'"></i>
							</div>
							<div class="col-md-9 radioset">
								<input type="radio" name="'.$thisID.'" id="'.$thisID.'yes" value="on" '. ($binds[$i[0]] == "on"?"CHECKED":"") .'>
								<label for="'.$thisID.'yes">'. _("Yes").'</label>
								<input type="radio" name="'.$thisID.'" id="'.$thisID.'no" value="off" '.($binds[$i[0]] == "on"?"":"CHECKED") .'>
								<label for="'.$thisID.'no">'. _("No").'</label>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<span id="'.$thisID.'-help" class="help-block fpbx-help-block">'. _("Use "). $thisTitle .'</span>
				</div>
			</div>
		</div>
		<!--END '.$thisTitle.'-->
		';
		$lastproto = $p;
	}
	$cbs .= '</div>';
	$protohtml .= $cbs;

	// Now display a section for each one.
	foreach ($binds as $ip => $stat) {
		if ($stat != "on")
			continue;
		$vars = array(
			$p."port-$ip" => array(_("Port to Listen On"),"","port", $ip),
			$p."domain-$ip" => array(_("Domain the transport comes from"),"","domain", $ip),
			$p."extip-$ip" => array(_("External IP Address"), _("If blank, will use the default settings"), "extip", $ip),
			$p."localnet-$ip" => array(_("Local network"), _("You may use this to to define an additional local network per interface."), "localnet", $ip),
		);
		foreach ($vars as $v => $t) {
			$thisID = str_replace(array('.', '-'), '' , $v);
			if (!empty($t[1])) {
				$udphtml  .= '
				<!--'.$t[0].'-->
				<div class="element-container">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="form-group">
									<div class="col-md-3">
										<label class="control-label" for="'.$thisID.'">'. $t[0] .'</label>
										<i class="fa fa-question-circle fpbx-help-icon" data-for="'.$thisID.'"></i>
									</div>
									<div class="col-md-9">
										<input type="text" class="form-control '.$t[2].'" data-orig="'.$this->getConfig($v).'" data-ip="'.$t[3].'" id="'.$thisID.'" name="'.$v.'" value="'.$this->getConfig($v).'">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<span id="'.$thisID.'-help" class="help-block fpbx-help-block  '.(empty($t[1])?'hidden':'').'">'.$t[1].'</span>
						</div>
					</div>
				</div>
				<!--END '.$t[0].'-->
				';
			} else {
				$udphtml  .= '
				<!--'.$t.'-->
				<div class="element-container">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="form-group">
									<div class="col-md-3">
										<label class="control-label" for="'.$thisID.'">'. $t[0] .'</label>
									</div>
									<div class="col-md-9">
										<input type="text" class="form-control '.$t[2].'" data-orig="'.$this->getConfig($v).'" data-ip="'.$t[3].'" id="'.$thisID.'" name="'.$v.'" value="'.$this->getConfig($v).'">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--END '.$t.'-->
				';
			}
		}
		$bindthml .= '
			<div class="section-title" data-for="pjsbind.'.$p.'"><h3>
				<i class="fa fa-minus"></i> '.$p.'</h3>
			</div>
			<div class="section" data-id="pjsbind.'.$p.'">
			'.$udphtml.'
			</div>
		';
		unset($udphtml);
	}
}
?>
<form name="pjsipform" id="pjsipform" class="fpbx-submit" action="" method="POST">
<input type="hidden" name="category" value="pjsip">
<input type="hidden" name="Submit" value="Submit">
<div class="section-title" data-for="pjsmisc"><h3>
	<i class="fa fa-minus"></i> <?php echo _("Misc PJSip Settings")?></h3>
</div>
<div class="section" data-id="pjsmisc">
	<!--Allow Guests-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="allowguests"><?php echo _("Allow Guests") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="allowguests"></i>
						</div>
						<div class="col-md-9 radioset">
							<input type="radio" name="allowguests" id="allowguestsyes" value="yes" <?php echo ($this->getConfig("allowguest") == "yes"?"CHECKED":"") ?>>
							<label for="allowguestsyes"><?php echo _("Yes");?></label>
							<input type="radio" name="allowguests" id="allowguestsno" value="no" <?php echo ($this->getConfig("allowguest") == "yes"?"":"CHECKED") ?>>
							<label for="allowguestsno"><?php echo _("No");?></label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="allowguests-help" class="help-block fpbx-help-block"><?php echo _("When set Asterisk will allow Guest SIP calls and send them to the Default SIP context. Turning this off will keep anonymous SIP calls from entering the system. Doing such will also stop 'Allow Anonymous Inbound SIP Calls' from functioning. Allowing guest calls but rejecting the Anonymous SIP calls below will enable you to see the call attempts and debug incoming calls that may be mis-configured and appearing as guests.")?></span>
			</div>
		</div>
	</div>
	<!--END Allow Guests-->
	<!--Show Advanced Settings-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="showadvanced"><?php echo _("Show Advanced Settings") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="showadvanced"></i>
						</div>
						<div class="col-md-9 radioset">
							<input type="radio" name="showadvanced" id="showadvancedyes" value="yes" <?php echo ( $this->getConfig("showadvanced") == "yes"?"CHECKED":"") ?>>
							<label for="showadvancedyes"><?php echo _("Yes");?></label>
							<input type="radio" name="showadvanced" id="showadvancedno" value="no" <?php echo ( $this->getConfig("showadvanced") == "yes"?"":"CHECKED") ?>>
							<label for="showadvancedno"><?php echo _("No");?></label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="showadvanced-help" class="help-block fpbx-help-block"><?php echo _("Show Advanced Settings")?></span>
			</div>
		</div>
	</div>
	<!--END Show Advanced Settings-->
</div>
<div class="section-title" data-for="pjstx"><h3>
	<i class="fa fa-minus"></i> <?php echo _("Transports")?></h3>
</div>
<div class="section" data-id="pjstx">
	<div class="well well-info">
		<?php echo _("Note that the interface is only displayed for your information, and is not referenced by asterisk.")?>
		<?php echo _("Also be warned: After you enable/disable a transport, asterisk needs to be <strong>restarted</strong>, not just reloaded.")?>
	</div>
</div>
<?php echo $protohtml?>
<?php echo $bindthml?>
</form>
