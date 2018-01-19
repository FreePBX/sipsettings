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
		if ($vals[2] != "inet" && $vals[3] != "mtu") {
			continue;
		}

		// Some versions of 'ip' have a backslash after the int name
		if ($vals[4] == "scope" && $vals[5] == "host") {
			$intname = rtrim($vals[6], '\\');
		} else {
			$intname = rtrim($vals[8], '\\');
		}

		// Strip netmask off the end of the IP address
		$ret = preg_match("/(\d*.\d*.\d*.\d*)[\/(\d*+)]*/", $vals[3], $ip);

		$interfaces[$intname] = array($ip[1], $intname, $ip[2]);
	}
}

$protocols = $this->getConfig("protocols");
$protohtml = $udphtml = $bindhtml = '';
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
		if ($stat != "on") {
			continue;
		}
		// ws and wss are not configurable
		if (strpos($p, "ws") === 0) {
			continue;
		}
		$vars = array(
			$p."port-$ip" => array(_("Port to Listen On"),_("The port that this transport should listen on"),"port", $ip),
			$p."domain-$ip" => array(_("Domain the transport comes from"),_("Typically used with SIP calling. Example user@domain, where domain is the value that would be entered here"),"domain", $ip),
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
		$bindhtml .= '
			<div class="section-title" data-for="pjsbind.'.$p.'"><h3>
				<i class="fa fa-minus"></i> '.$ip.' ('.$p.')</h3>
			</div>
			<div class="section" data-id="pjsbind.'.$p.'">
			'.$udphtml.'
			</div>
		';
		unset($udphtml);
	}
}
?>
<input type="hidden" name="category" value="pjsip">
<input type="hidden" name="Submit" value="Submit">
<div class="section-title" data-for="pjsmisc"><h3>
	<i class="fa fa-minus"></i> <?php echo _("Misc PJSip Settings")?></h3>
</div>
<div class="section" data-id="pjsmisc">
	<!--Allow reload-->
	<div class="element-container">
		<div class="row">
			<div class="form-group">
				<div class="col-md-3">
					<label class="control-label" for="allow_reload"><?php echo _("Allow Reload") ?></label>
					<i class="fa fa-question-circle fpbx-help-icon" data-for="allow_reload"></i>
				</div>
				<div class="col-md-9 radioset">
					<input type="radio" name="allow_reload" id="allow_reloadyes" value="yes" <?php echo ( $this->getConfig("pjsip_allow_reload") == "yes"?"CHECKED":"") ?>>
					<label for="allow_reloadyes"><?php echo _("Yes");?></label>
					<input type="radio" name="allow_reload" id="allow_reloadno" value="no" <?php echo ( $this->getConfig("pjsip_allow_reload") == "yes"?"":"CHECKED") ?>>
					<label for="allow_reloadno"><?php echo _("No");?></label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="allow_reload-help" class="help-block fpbx-help-block"><?php echo _("Allow this transport to be reloaded when res_pjsip is reloaded. This option defaults to 'no' because reloading a transport may disrupt in-progress calls.")?></span>
			</div>
		</div>
	</div>
	<!--END Allow reload-->
	<!--Show Advanced Settings-->
	<div class="element-container">
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
		<div class="row">
			<div class="col-md-12">
				<span id="showadvanced-help" class="help-block fpbx-help-block"><?php echo _("Show Advanced Settings")?></span>
			</div>
		</div>
	</div>
	<!--END Show Advanced Settings-->
</div>

	<div class="section-title" data-for="pjtls"><h3>
		<i class="fa fa-minus"></i> <?php echo _("TLS/SSL/SRTP Settings")?></h3>
	</div>
	<div class="section" data-id="pjtls">
	<?php if(!\FreePBX::Modules()->moduleHasMethod("certman","getDefaultCertDetails")) {?>
			<div class="element-container">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="calistfile"><?php echo _("CA Chain File") ?></label>
						</div>
						<div class="col-md-9">
						<input type="text" class="form-control" name="calistfile" placeholder="/etc/asterisk/keys/integration/ca-bundle.crt" value="<?php echo $this->getConfig("calistfile"); ?>"></input>
						</div>
					</div>
				</div>
			</div>
			<div class="element-container">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="certfile"><?php echo _("Certificate File") ?></label>
						</div>
						<div class="col-md-9">
							<input type="text" class="form-control" name="certfile" placeholder="/etc/asterisk/keys/integration/webserver.crt" value="<?php echo $this->getConfig("certfile"); ?>"></input>
						</div>
					</div>
				</div>
			</div>
			<div class="element-container">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="privkeyfile"><?php echo _("Private Key File") ?></label>
						</div>
						<div class="col-md-9">
							<input type="text" class="form-control" name="privkeyfile" placeholder="/etc/asterisk/keys/integration/webserver.key" value="<?php echo $this->getConfig("privkeyfile"); ?>"></input>
						</div>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<div class="element-container">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="pjsipcalistfile"><?php echo _("Certificate Manager") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="pjsipcalistfile"></i>
						</div>
						<div class="col-md-9">
							<select class="form-control" id="pjsipcalistfile" name="pjsipcertid">
								<option value=""><?php echo "--"._("Select a Certificate")."--"?></option>
								<?php $cid = $this->getConfig("pjsipcertid"); foreach(\FreePBX::Certman()->getAllManagedCertificates() as $cert) { ?>
									<option value="<?php echo $cert['cid']?>" <?php echo $cid == $cert['cid'] ? 'selected' : ''?>><?php echo $cert['basename']?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<span id="pjsipcalistfile-help" class="help-block fpbx-help-block"><?php echo _("Select a certificate to use for the TLS transport. These are configured in the module Certificate Manager")?></span>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="element-container">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="method"><?php echo _("SSL Method") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="method"></i>
					</div>
					<div class="col-md-9 radioset">
						<select class="form-control" id="method" name="method">
							<option value="default" <?php echo ($this->getConfig("method") == "default"?"selected":"") ?>><?php echo _("Default");?></option>
							<option value="tlsv1" <?php echo ($this->getConfig("method") == "tlsv1"?"selected":"") ?>>tlsv1</option>
							<option value="sslv2" <?php echo ($this->getConfig("method") == "sslv2"?"selected":"") ?>>sslv2 (<?php echo _('Insecure')?>)</option>
							<option value="sslv3" <?php echo ($this->getConfig("method") == "sslv3"?"selected":"") ?>>sslv3 (<?php echo _('Insecure')?>)</option>
							<option value="sslv23" <?php echo ($this->getConfig("method") == "sslv23"?"selected":"") ?>>sslv23 (<?php echo _('Insecure')?>)</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<span id="method-help" class="help-block fpbx-help-block"><?php echo _("Method of SSL transport (TLS ONLY). The default is currently TLSv1, but may change with future releases.")?></span>
				</div>
			</div>
		</div>
		<div class="element-container">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="verify_client"><?php echo _("Verify Client") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="verify_client"></i>
					</div>
					<div class="col-md-9 radioset">
						<input type="radio" name="verify_client" id="verify_clientyes" value="yes" <?php echo ($this->getConfig("verify_client") == "yes"?"CHECKED":"") ?>>
						<label for="verify_clientyes"><?php echo _("Yes");?></label>
						<input type="radio" name="verify_client" id="verify_clientno" value="no" <?php echo ($this->getConfig("verify_client") == "no"?"CHECKED":"") ?>>
						<label for="verify_clientno"><?php echo _("No");?></label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<span id="verify_client-help" class="help-block fpbx-help-block"><?php echo _("Require verification of client certificate (TLS ONLY).")?></span>
				</div>
			</div>
		</div>
		<div class="element-container">
			<div class="row">
				<div class="form-group">
					<div class="col-md-3">
						<label class="control-label" for="verify_server"><?php echo _("Verify Server") ?></label>
						<i class="fa fa-question-circle fpbx-help-icon" data-for="verify_server"></i>
					</div>
					<div class="col-md-9 radioset">
						<input type="radio" name="verify_server" id="verify_serveryes" value="yes" <?php echo ($this->getConfig("verify_server") == "yes"?"CHECKED":"") ?>>
						<label for="verify_serveryes"><?php echo _("Yes");?></label>
						<input type="radio" name="verify_server" id="verify_serverno" value="no" <?php echo ($this->getConfig("verify_server") == "no"?"CHECKED":"") ?>>
						<label for="verify_serverno"><?php echo _("No");?></label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<span id="verify_server-help" class="help-block fpbx-help-block"><?php echo _("Require verification of server certificate (TLS ONLY).")?></span>
				</div>
			</div>
		</div>
	</div>

<div class="section-title" data-for="pjstx"><h3>
	<i class="fa fa-minus"></i> <?php echo _("Transports")?></h3>
</div>
<div class="section" data-id="pjstx">
	<div class="well well-info">
		<?php echo _("Note that the interface is only displayed for your information, and is not referenced by asterisk.")?>
		<?php if(version_compare($this->FreePBX->Config->get('ASTVERSION'),"13.8","ge")) { ?>
			<!-- Not sure if we need a warning here -->
			<?php echo sprintf(_("You have Asterisk %s which no longer needs to be restarted for transport changes. Reloading after changing transports does have the possibility to drop calls."),$this->FreePBX->Config->get('ASTVERSION'))?>
		<?php } else { ?>
			<?php echo _("Also be warned: After you enable/disable a transport, asterisk needs to be <strong>restarted</strong>, not just reloaded.")?>
		<?php } ?>
	</div>
</div>
<?php echo $protohtml?>
<?php echo $bindhtml?>