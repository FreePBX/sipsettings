<?php
// vim: set ai ts=4 sw=4 ft=phtml:
$localnets = $this->getConfig('localnets');
if (!$localnets) {
	$localnets = array();
}

$externip = $this->getConfig('externip');
if (!$externip) {
	$externip = ""; // Ensure any failure is always an empty string
}


$ice_blacklist = $this->getConfig('ice-blacklist');
$ice_blacklist = !empty($ice_blacklist) ? $ice_blacklist : array(array("address" => "","subnet" => ""));
$ice_host_candidates = $this->getConfig('ice-host-candidates');
$ice_host_candidates = !empty($ice_host_candidates) ? $ice_host_candidates : array(array("local" => "","advertised" => ""));

$add_local_network_field = _("Add Local Network Field");
$submit_changes = _("Submit Changes");

?>
<form autocomplete="off" action="" method="post" class="fpbx-submit" id="sipsettings" name="sipsettings">
<input type="hidden" name="category" value="general">
<input type="hidden" name="Submit" value="Submit">
<div class="section-title" data-for="sssecurity">
	<h3><i class="fa fa-minus"></i><?php echo _("Security Settings") ?></h3>
</div>
<div class="section" data-id="sssecurity">
	<!--Allow Anonymous Inbound SIP Calls-->
	<div class="element-container">
		<div class="row">
			<div class="form-group">
				<div class="col-md-3">
					<label class="control-label" for="allowanon"><?php echo _("Allow Anonymous Inbound SIP Calls") ?></label>
					<i class="fa fa-question-circle fpbx-help-icon" data-for="allowanon"></i>
				</div>
				<div class="col-md-9 radioset">
					<input type="radio" name="allowanon" id="allowanonyes" value="Yes" <?php echo ($this->getConfig("allowanon") == "Yes"?"CHECKED":"") ?>>
					<label for="allowanonyes"><?php echo _("Yes");?></label>
					<input type="radio" name="allowanon" id="allowanonno" value="No" <?php echo ($this->getConfig("allowanon") == "Yes"?"":"CHECKED") ?>>
					<label for="allowanonno"><?php echo _("No");?></label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="allowanon-help" class="help-block fpbx-help-block"><?php echo _("Allowing Inbound Anonymous SIP calls means that you will allow any call coming in form an un-known IP source to be directed to the 'from-pstn' side of your dialplan. This is where inbound calls come in. Although FreePBX severely restricts access to the internal dialplan, allowing Anonymous SIP calls does introduced additional security risks. If you allow SIP URI dialing to your PBX or use services like ENUM, you will be required to set this to Yes for Inbound traffic to work. This is NOT an Asterisk sip.conf setting, it is used in the dialplan in conjuction with the Default Context. If that context is changed above to something custom this setting may be rendered useless as well as if 'Allow SIP Guests' is set to no.")?></span>
			</div>
		</div>
	</div>
	<!--END Allow Anonymous Inbound SIP Calls-->
	<!-- TLS Port Settings -->
	<div class="element-container">
		<div class="row">
			<div class="form-group">
				<div class="col-md-3">
					<label class="control-label" for="tlsowner"><?php echo _("Default TLS Port Assignment") ?></label>
					<i class="fa fa-question-circle fpbx-help-icon" data-for="tlsowner"></i>
				</div>
				<div class="col-md-9 radioset">
<?php
$tlsowners = array("sip" => _("Chan SIP"), "pjsip" => _("PJSip"));
$owner = $this->getTlsPortOwner();
$binds = $this->getBinds();
foreach ($tlsowners as $chan => $txt) {
	if ($owner === $chan) {
		$checked = "checked";
	} else {
		$checked = "";
	}

	// Is this protocol available?
	if (isset($binds[$chan])) {
		// Is it listening for TLS anywhere?
		$foundtls = false;
		foreach ($binds[$chan] as $protocols) {
			foreach ($protocols as $p => $pport) {
				if ($p == "tls") {
					$foundtls = true;
					break;
				}
			}
		}
		if ($foundtls) {
			$disabled = "";
		} else {
			$disabled = "disabled";
		}
	} else {
		$disabled = "disabled";
	}
	print "<input type='radio' name='tlsportowner' id='tls-$chan' value='$chan' $disabled $checked>\n";
	print "<label for='tls-$chan'>$txt</label>\n";
}
?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="tlsowner-help" class="help-block fpbx-help-block"><?php echo _("This lets you explicitly control the SIP Protocol that listens on the default SIP TLS port (5061). If an option is not available, it is because that protocol is not enabled, or, that protocol does not have TLS enabled. If you change this, you will have to restart Asterisk"); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="section-title" data-for="ssnat">
	<h3><i class="fa fa-minus"></i><?php echo _("NAT Settings") ?></h3>
</div>
<div class="section" data-id="ssnat">
	<div class="alert alert-info" role="alert"><?php echo _("These settings apply to both chan_sip and chan_pjsip."); ?></div>
	<!--External Address-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="externip"><?php echo _("External Address") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="externip"></i>
						</div>
						<div class="col-md-9">
							<input type="text" class="form-control localnet validate=ip" id="externip" name="externip" value="<?php echo $externip ?>">
							<button class='btn btn-default' id='autodetect'><?php echo _("Detect Network Settings")?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="externip-help" class="help-block fpbx-help-block"><?php echo _("This address will be provided to clients if NAT is enabled and detected")?></span>
			</div>
		</div>
	</div>
	<!--END External Address-->
	<?php
	// Are there any MORE nets?
	// Remove the first one that we've displayed
	$localnetstmp = $localnets;
	unset ($localnetstmp[0]);
	// Now loop through any more, if they exist.
	$lnhtm = '';
	foreach ($localnetstmp as $id => $arr) {
		$lnhtm .= '<div class = "lnet form-group form-inline" data-nextid='.($id+1).'>';
		$lnhtm .= '	<input type="text" name="localnets['.$id.'][net]" class="form-control localnet network validate-ip"  pattern="^$|^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="'.$arr['net'].'">/';
		$lnhtm .= '	<input type="text" name="localnets['.$id.'][mask]" class="form-control netmask cidr validate-netmask" pattern="^$|^((?:[1-9])|(?:[1-2][0-9])|(?:3[0-2]))$|^(((255\.){3}(255|254|252|248|240|224|192|128|0+))|((255\.){2}(255|254|252|248|240|224|192|128|0+)\.0)|((255\.)(255|254|252|248|240|224|192|128|0+)(\.0+){2})|((255|254|252|248|240|224|192|128|0+)(\.0+){3}))$" value="'.$arr['mask'].'">';
		$lnhtm .= '</div>';
	}
	?>
	<!--Local Networks-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="localbnetsw"><?php echo _("Local Networks") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="localbnetsw"></i>
						</div>
						<div class="col-md-9">
							<div class = "lnet form-group form-inline" data-nextid=1>
								<input type="text" name="localnets[0][net]" class="form-control localnet network validate-ip"  pattern="^$|^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="<?php echo isset($localnets[0]['net']) ? $localnets[0]['net'] : '' ?>"> /
								<input type="text" name="localnets[0][mask]" class="form-control netmask cidr validate-netmask" pattern="^$|^((?:[1-9])|(?:[1-2][0-9])|(?:3[0-2]))$|^(((255\.){3}(255|254|252|248|240|224|192|128|0+))|((255\.){2}(255|254|252|248|240|224|192|128|0+)\.0)|((255\.)(255|254|252|248|240|224|192|128|0+)(\.0+){2})|((255|254|252|248|240|224|192|128|0+)(\.0+){3}))$" value="<?php echo isset($localnets[0]['mask']) ? $localnets[0]['mask'] : ''?>">
							</div>
							<?php echo $lnhtm?>
							<input type="button" id="localnet-add" value="<?php echo $add_local_network_field ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="localbnetsw-help" class="help-block fpbx-help-block"><?php echo _("Local network settings in the form of ip/cidr or ip/netmask. For networks with more than 1 LAN subnets, use the Add Local Network Field button for more fields. Blank fields will be ignored.")?></span>
			</div>
		</div>
	</div>
	<!--END Local Networks-->
</div>
<div class="section-title" data-for="ssrtp">
	<h3><i class="fa fa-minus"></i><?php echo _("RTP Settings") ?></h3>
</div>
<div class="section" data-id="ssrtp">
	<!--RTP Port Ranges-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="rtpw"><?php echo _("RTP Port Ranges") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="rtpw"></i>
						</div>
						<div class="col-md-9">
							<div class="row">
								<div class="col-sm-1">
									<label for="rtpstart"><b><?php echo _("Start").":"?></b></label>
								</div>
								<div class="col-sm-11">
									<input type='number' name='rtpstart' class='form-control validate-int' value='<?php echo $this->getConfig('rtpstart')?>'>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-1">
									<label for="rtpend"><b><?php echo _("End").":"?></b></label>
								</div>
								<div class="col-sm-11">
									<input type='number' name='rtpend'   class='form-control validate-int' value='<?php echo $this->getConfig('rtpend') ?>'>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="rtpw-help" class="help-block fpbx-help-block"><?php echo _("The starting and ending RTP port range")?></span>
			</div>
		</div>
	</div>
	<!--END RTP Port Ranges-->
	<!--RTP Checksums-->
	<div class="element-container">
		<div class="row">
			<div class="form-group">
				<div class="col-md-3">
					<label class="control-label" for="rtpchecksums"><?php echo _("RTP Checksums") ?></label>
					<i class="fa fa-question-circle fpbx-help-icon" data-for="rtpchecksums"></i>
				</div>
				<div class="col-md-9 radioset">
					<input type="radio" name="rtpchecksums" id="rtpchecksumsyes" value="yes" <?php echo (strtolower($this->getConfig("rtpchecksums")) == "yes"?"checked":"") ?>>
					<label for="rtpchecksumsyes"><?php echo _("Yes");?></label>
					<input type="radio" name="rtpchecksums" id="rtpchecksumsno" value="No" <?php echo (strtolower($this->getConfig("rtpchecksums")) != "yes"?"checked":"") ?>>
					<label for="rtpchecksumsno"><?php echo _("No");?></label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="rtpchecksums-help" class="help-block fpbx-help-block"><?php echo _("Whether to enable or disable UDP checksums on RTP traffic")?></span>
			</div>
		</div>
	</div>
	<!--END RTP Checksums-->
	<!--Strict RTP-->
	<div class="element-container">
		<div class="row">
			<div class="form-group">
				<div class="col-md-3 radioset">
					<label class="control-label" for="strictrtp"><?php echo _("Strict RTP") ?></label>
					<i class="fa fa-question-circle fpbx-help-icon" data-for="strictrtp"></i>
				</div>
				<div class="col-md-9 radioset">
					<input type="radio" name="strictrtp" id="strictrtpyes" value="Yes" <?php echo (strtolower($this->getConfig("strictrtp")) == "yes"?"checked":"") ?>>
					<label for="strictrtpyes"><?php echo _("Yes");?></label>
					<input type="radio" name="strictrtp" id="strictrtpno" value="No" <?php echo (strtolower($this->getConfig("strictrtp")) != "yes"?"checked":"") ?>>
					<label for="strictrtpno"><?php echo _("No");?></label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="strictrtp-help" class="help-block fpbx-help-block"><?php echo _("This will drop RTP packets that do not come from the source of the RTP stream. It is unusual to turn this off")?></span>
			</div>
		</div>
	</div>
	<!--END Strict RTP-->
	<!--STUN Server Address-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="stunaddr"><?php echo _("STUN Server Address") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="stunaddr"></i>
						</div>
						<div class="col-md-9">
							<input type="text" class="form-control" id="stunaddr" name="stunaddr" value="<?php echo $this->getConfig('stunaddr') ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="stunaddr-help" class="help-block fpbx-help-block"><?php echo _("Hostname or address for the STUN server used when determining the external IP address and port an RTP session can be reached at. The port number is optional. If omitted the default value of 3478 will be used. This option is blank by default. (A list of STUN servers: http://wiki.freepbx.org/x/YQCUAg)")?></span>
			</div>
		</div>
	</div>
	<!--END STUN Server Address-->
	<!--TURN Server Address-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="turnaddr"><?php echo _("TURN Server Address") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="turnaddr"></i>
						</div>
						<div class="col-md-9">
							<input type="text" class="form-control" id="turnaddr" name="turnaddr" value="<?php echo $this->getConfig('turnaddr') ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="turnaddr-help" class="help-block fpbx-help-block"><?php echo _("Hostname or address for the TURN server to be used as a relay. The port number is optional. If omitted the default value of 3478 will be used. This option is blank by default.")?></span>
			</div>
		</div>
	</div>
	<!--END TURN Server Address-->
	<!--TURN Server Username-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="turnusername"><?php echo _("TURN Server Username") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="turnusername"></i>
						</div>
						<div class="col-md-9">
							<input type="text" class="form-control" id="turnusername" name="turnusername" value="<?php echo $this->getConfig('turnusername') ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="turnusername-help" class="help-block fpbx-help-block"><?php echo _("Username used to authenticate with TURN relay server. This option is disabled by default.")?></span>
			</div>
		</div>
	</div>
	<!--END TURN Server Username-->
	<!--TURN Server Password-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="turnpassword"><?php echo _("TURN Server Password") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="turnpassword"></i>
						</div>
						<div class="col-md-9">
							<input type="password" class="form-control clicktoedit" id="turnpassword" name="turnpassword" value="<?php echo $this->getConfig('turnpassword') ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="turnpassword-help" class="help-block fpbx-help-block"><?php echo _("Password used to authenticate with TURN relay server. This option is disabled by default.")?></span>
			</div>
		</div>
	</div>
	<!--END TURN Server Password-->
</div>
<div class="section-title" data-for="ice-blacklist">
	<h3><i class="fa fa-minus"></i><?php echo _("ICE Blacklist") ?></h3>
</div>
<div class="section" data-id="ice-blacklist">
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="panel-title">
				<a data-toggle="collapse" data-target="#moreinfo-ice-blacklist" style="cursor:pointer;"><i class="glyphicon glyphicon-info-sign"></i></a>&nbsp;&nbsp;&nbsp;<?php echo _("What is ICE Blacklist?")?></div>
		</div>
		<!--At some point we can probably kill this... Maybe make is a 1 time panel that may be dismissed-->
		<div class="panel-body collapse" id="moreinfo-ice-blacklist">
			<p><?php echo _("Subnets to exclude from ICE host, srflx and relay discovery. This is useful to optimize the ICE process where a system has multiple host address ranges and/or physical interfaces and certain of them are not expected to be used for RTP. For example, VPNs and local interconnections may not be suitable or necessary for ICE. Multiple subnets may be listed. If left unconfigured, all discovered host addresses are used.")?></p>
			<p><?php echo _("The format for these overrides is: [address] / [subnet]")?></p>
			<p><?php echo _("This is most commonly used for WebRTC")?></p>
		</div>
	</div>
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for=""><?php echo _("IP Addresses")?></label>
						</div>
						<div class="col-md-9">
							<?php $i = 0;foreach($ice_blacklist as $can) {?>
								<div class="form-group form-inline">
									<input type="hidden" id="ice_blacklist_count" name="ice_blacklist_count[]" value="<?php echo $i?>"><input type="text" id="ice_blacklist_ip_<?php echo $i?>" name="ice_blacklist_ip_<?php echo $i?>" class="form-control ice-blacklist" pattern="^$|^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="<?php echo $can['address']?>"> / <input type="text" id="ice_blacklist_subnet_<?php echo $i?>" name="ice_blacklist_subnet_<?php echo $i?>" class="form-control"  pattern="^$|^((?:[1-9])|(?:[1-2][0-9])|(?:3[0-2]))$|^(((255\.){3}(255|254|252|248|240|224|192|128|0+))|((255\.){2}(255|254|252|248|240|224|192|128|0+)\.0)|((255\.)(255|254|252|248|240|224|192|128|0+)(\.0+){2})|((255|254|252|248|240|224|192|128|0+)(\.0+){3}))$" value="<?php echo $can['subnet']?>">
								</div>
							<?php $i++;} ?>
							<div id="ice-blacklist-buttons">
								<div>
									<button id="ice-blacklist-add" class="btn btn-default"><?php echo _("Add Address")?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="section-title" data-for="ice-host-candidates">
	<h3><i class="fa fa-minus"></i><?php echo _("ICE Host Candidates") ?></h3>
</div>
<div class="section" data-id="ice-host-candidates">
	<div class="panel panel-info">
		<div class="panel-heading">
			<div class="panel-title">
				<a data-toggle="collapse" data-target="#moreinfo-ice-host-candidates" style="cursor:pointer;"><i class="glyphicon glyphicon-info-sign"></i></a>&nbsp;&nbsp;&nbsp;<?php echo _("What is ICE Host Candidates?")?></div>
		</div>
		<!--At some point we can probably kill this... Maybe make is a 1 time panel that may be dismissed-->
		<div class="panel-body collapse" id="moreinfo-ice-host-candidates">
			<p><?php echo _("When Asterisk is behind a static one-to-one NAT and ICE is in use, ICE will expose the server's internal IP address as one of the host candidates. Although using STUN (see the 'stunaddr' configuration option) will provide a publicly accessible IP, the internal IP will still be sent to the remote peer. To help hide the topology of your internal network, you can override the host candidates that Asterisk will send to the remote peer.")?></p>
			<p><?php echo _("IMPORTANT: Only use this functionality when your Asterisk server is behind a one-to-one NAT and you know what you're doing. If you do define anything here, you almost certainly will NOT want to specify 'stunaddr' or 'turnaddr' above.")?></p>
			<p><?php echo _("The format for these overrides is: [local address] => [advertised address]>")?></p>
			<p><?php echo _("This is most commonly used for WebRTC")?></p>
		</div>
	</div>
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for=""><?php echo _("Candidates")?></label>
						</div>
						<div class="col-md-9">
							<?php $i = 0;foreach($ice_host_candidates as $can) {?>
								<div class="form-group form-inline">
									<input type="hidden" id="ice_host_candidates_count" name="ice_host_candidates_count[]" value="<?php echo $i?>"><input type="text" id="ice_host_candidates_local_<?php echo $i?>" name="ice_host_candidates_local_<?php echo $i?>" class="form-control ice-host-candidate" pattern="^$|^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="<?php echo $can['local']?>"> => <input type="text" id="ice_host_candidates_advertised_<?php echo $i?>" name="ice_host_candidates_advertised_<?php echo $i?>" class="form-control" pattern="^$|^([0-9]{1,3}\.){3}[0-9]{1,3}$" value="<?php echo $can['advertised']?>">
								</div>
							<?php } ?>
							<div id="ice-host-candidates-buttons">
								<button id="ice-host-candidates-add" class="btn btn-default"><?php echo _("Add Address")?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="section-title" data-for="webrtc">
	<h3><i class="fa fa-minus"></i><?php echo _("WebRTC Settings") ?></h3>
</div>
<div class="section" data-id="webrtc">
	<!--STUN Server Address-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="webrtcstunaddr"><?php echo _("STUN Server Address") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="webrtcstunaddr"></i>
						</div>
						<div class="col-md-9">
							<input type="text" class="form-control" id="webrtcstunaddr" name="webrtcstunaddr" value="<?php echo $this->getConfig('webrtcstunaddr') ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="webrtcstunaddr-help" class="help-block fpbx-help-block"><?php echo _("Hostname or address for the STUN server used when determining the external IP address and port an RTP session can be reached at. The port number is optional. If omitted the default value of 3478 will be used. This option is blank by default. (A list of STUN servers: http://wiki.freepbx.org/x/YQCUAg)")?></span>
			</div>
		</div>
	</div>
	<!--END STUN Server Address-->
	<!--TURN Server Address-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="webrtcturnaddr"><?php echo _("TURN Server Address") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="webrtcturnaddr"></i>
						</div>
						<div class="col-md-9">
							<input type="text" class="form-control" id="webrtcturnaddr" name="webrtcturnaddr" value="<?php echo $this->getConfig('webrtcturnaddr') ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="webrtcturnaddr-help" class="help-block fpbx-help-block"><?php echo _("Hostname or address for the TURN server to be used as a relay. The port number is optional. If omitted the default value of 3478 will be used. This option is blank by default.")?></span>
			</div>
		</div>
	</div>
	<!--END TURN Server Address-->
	<!--TURN Server Username-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="webrtcturnusername"><?php echo _("TURN Server Username") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="webrtcturnusername"></i>
						</div>
						<div class="col-md-9">
							<input type="text" class="form-control" id="webrtcturnusername" name="webrtcturnusername" value="<?php echo $this->getConfig('webrtcturnusername') ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="webrtcturnusername-help" class="help-block fpbx-help-block"><?php echo _("Username used to authenticate with TURN relay server. This option is disabled by default.")?></span>
			</div>
		</div>
	</div>
	<!--END TURN Server Username-->
	<!--TURN Server Password-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="webrtcturnpassword"><?php echo _("TURN Server Password") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="webrtcturnpassword"></i>
						</div>
						<div class="col-md-9">
							<input type="password" class="form-control clicktoedit" id="webrtcturnpassword" name="webrtcturnpassword" value="<?php echo $this->getConfig('webrtcturnpassword') ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="webrtcturnpassword-help" class="help-block fpbx-help-block"><?php echo _("Password used to authenticate with TURN relay server. This option is disabled by default.")?></span>
			</div>
		</div>
	</div>
</div>
<div class="section-title" data-for="sscodecs">
	<h3><i class="fa fa-minus"></i><?php echo _("Audio Codecs") ?></h3>
</div>
<div class="section" data-id="sscodecs">
	<!--Codecs-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="codecw"><?php echo _("Codecs") ?></label>
						</div>
						<div class="col-md-9">
							<?php echo \show_help(_("This is the default Codec setting for new Trunks and Extensions."))?>
							<?php
							$seq = 1;
							echo '<ul class="sortable">';
							foreach (FreePBX::Sipsettings()->getCodecs('audio',true) as $codec => $codec_state) {
								$codec_trans = _($codec);
								$codec_checked = $codec_state ? 'checked' : '';
								echo '<li><a >'
									. '<img src="assets/sipsettings/images/arrow_up_down.png" height="16" width="16" border="0" alt="move" style="float:none; margin-left:-6px; margin-bottom:-3px;cursor:move" /> '
									. '<input type="checkbox" '
									. ($codec_checked ? 'value="'. $seq++ . '" ' : '')
									. 'name="voicecodecs[' . $codec . ']" '
									. 'id="'. $codec . '" '
									. 'class="audio-codecs" '
									. $codec_checked
									. ' />'
									. '<label for="'. $codec . '"> '
									. '<small>' . $codec_trans . '</small>'
									. " </label></a></li>\n";
							}
							echo '</ul>';
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--END Codecs-->
</div>
</form>
