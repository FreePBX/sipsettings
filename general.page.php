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

$add_local_network_field = _("Add Local Network Field");
$submit_changes = _("Submit Changes");

?>
<form autocomplete="off" action="" method="post" class=fpbx-submit" id="sipsettings" name="sipsettings">
<input type="hidden" name="category" value="general">
<div class="alert alert-info" role="alert"><strong><?php echo _("Items may moved! Please use the navigation on the right")?> <i class="fa fa-arrow-right pull-right"></i></strong></div>
<div class="section-title" data-for="sssecurity">
	<h3><i class="fa fa-minus"></i><?php echo _("Security Settings") ?></h3>
</div>
<div class="section" data-id="sssecurity">
	<!--Allow Anonymous Inbound SIP Calls-->
	<div class="element-container">
		<div class="row">
			<div class="col-md-12">
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
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="allowanon-help" class="help-block fpbx-help-block"><?php echo _("Allowing Inbound Anonymous SIP calls means that you will allow any call coming in form an un-known IP source to be directed to the 'from-pstn' side of your dialplan. This is where inbound calls come in. Although FreePBX severely restricts access to the internal dialplan, allowing Anonymous SIP calls does introduced additional security risks. If you allow SIP URI dialing to your PBX or use services like ENUM, you will be required to set this to Yes for Inbound traffic to work. This is NOT an Asterisk sip.conf setting, it is used in the dialplan in conjuction with the Default Context. If that context is changed above to something custom this setting may be rendered useless as well as if 'Allow SIP Guests' is set to no.")?></span>
			</div>
		</div>
	</div>
	<!--END Allow Anonymous Inbound SIP Calls-->
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
							<button id='autodetect'><?php echo _("Detect External IP")?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<span id="externip-help" class="help-block fpbx-help-block"><?php echo _("This address will be provided to clients if NAT is enabled and detecte")?></span>
			</div>
		</div>
	</div>
	<!--END External Address-->
	<?php
	// Are there any MORE nets?
	// Remove the first one that we've displayed
	unset ($localnets[0]);

	// Now loop through any more, if they exist.
	foreach ($localnets as $id => $arr) {
		$lnhtm .= '<div class = "lnet form-group form-inline" data-nextid='.($id+1).'>';
		$lnhtm .= '	<input type="text" name="localnets['.$id.'][net]" class="form-control localnet network validate=ip" value="'.$arr['net'].'">/';
		$lnhtm .= '	<input type="text" name="localnets['.$id.'][mask]" class="form-control netmask cidr validate-netmask" value="'.$arr['mask'].'">';	
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
								<input type="text" name="localnets[0][net]" class="form-control localnet network validate=ip" value="<?php echo $localnets[0]['net'] ?>"> /
								<input type="text" name="localnets[0][mask]" class="form-control netmask cidr validate-netmask" value="<?php echo $localnets[0]['mask'] ?>">
								<?php echo $lnhtm?>
							</div>
							<input type="button" id="localnet-add" value="<?php echo $add_local_network_field ?>" class="nat-settings" />
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
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="rtpchecksums"><?php echo _("RTP Checksums") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="rtpchecksums"></i>
						</div>
						<div class="col-md-9 radioset">
							<input type="radio" name="rtpchecksums" id="rtpchecksumsyes" value="Yes" <?php echo ($this->getConfig("rtpchecksums") == "Yes"?"CHECKED":"") ?>>
							<label for="rtpchecksumsyes"><?php echo _("Yes");?></label>
							<input type="radio" name="rtpchecksums" id="rtpchecksumsno" value="No" <?php echo ($this->getConfig("rtpchecksums") == "Yes"?"":"CHECKED") ?>>
							<label for="rtpchecksumsno"><?php echo _("No");?></label>
						</div>
					</div>
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
			<div class="col-md-12">
				<div class="row">
					<div class="form-group">
						<div class="col-md-3 radioset">
							<label class="control-label" for="strictrtp"><?php echo _("Strict RTP") ?></label>
							<i class="fa fa-question-circle fpbx-help-icon" data-for="strictrtp"></i>
						</div>
						<div class="col-md-9 radioset">
							<input type="radio" name="strictrtp" id="strictrtpyes" value="Yes" <?php echo ($this->getConfig("strictrtp") == "Yes"?"CHECKED":"") ?>>
							<label for="strictrtpyes"><?php echo _("Yes");?></label>
							<input type="radio" name="strictrtp" id="strictrtpno" value="No" <?php echo ($this->getConfig("strictrtp") == "Yes"?"":"CHECKED") ?>>
							<label for="strictrtpno"><?php echo _("No");?></label>
						</div>
					</div>
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
				<span id="stunaddr-help" class="help-block fpbx-help-block"><?php echo _("Hostname or address for the STUN server used when determining the external IP address and port an RTP session can be reached at. The port number is optional. If omitted the default value of 3478 will be used. This option is blank by default. (A list of STUN servers: https://gist.github.com/zziuni/3741933)")?></span>
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
							<input type="password" class="form-control" id="turnpassword" name="turnpassword" value="<?php echo $this->getConfig('turnpassword') ?>">
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
							<i class="fa fa-question-circle fpbx-help-icon" data-for="codecw"></i>
						</div>
						<div class="col-md-9">
							<?php
							$seq = 1;
							echo '<ul class="sortable">';
							foreach (FreePBX::Sipsettings()->getCodecs('audio',true) as $codec => $codec_state) {
								$codec_trans = _($codec);
								$codec_checked = $codec_state ? 'checked' : '';
								echo '<li><a href="#">'
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
		<div class="row">
			<div class="col-md-12">
				<span id="codecw-help" class="help-block fpbx-help-block"><?php echo _("This is the default Codec setting for new Trunks and Extensions.")?></span>
			</div>
		</div>
	</div>
	<!--END Codecs-->
</div>
</form>

<script type="text/javascript">
$(document).ready(function(){
	$("#localnet-add").click(function() { addLocalnet("", "") });
	$("#autodetect").click(function(e) { e.preventDefault(); detectExtern() });
	var path = window.location.pathname.toString().split('/');
	path[path.length - 1] = 'ajax.php';
	// Oh look, IE. Hur Dur, I'm a bwowsah.
	if (typeof(window.location.origin) == 'undefined') {
		window.location.origin = window.location.protocol+'//'+window.location.host;
	}
	window.ajaxurl = window.location.origin + path.join('/');
	// This assumes the module name is the first param.
	window.modulename = window.location.search.split(/\?|&/)[1].split('=')[1];
});

function detectExtern() {
	$("#externip").val("").attr("placeholder", "Loading...").attr("disabled", true);
	$.ajax({
		url: window.ajaxurl,
		data: { command: 'getnetworking', module: window.modulename },
		success: function(data) { updateAddrAndRoutes(data); },
	});
}

function updateAddrAndRoutes(data) {
	console.log(data);
	window.d = data;
	$("#externip").val("").attr("placeholder", "Enter IP Address").attr("disabled", false);
	if (data.externip != false) {
		$("#externip").val(data.externip);
	}

	// Now, go through our detected networks, and see if we need to add them.
	$.each(d.routes, function() {
		var sel = ".network[value='"+this[0]+"']";
		if (!$(sel).length) {
			// Add it
			addLocalnet(this[0], this[1]);
		}
	});
}


function addLocalnet(net, cidr) {
	// We'd like a new one, please.
	var last = $(".lnet:last");
	var ourid = last.data('nextid');
	var nextid = ourid + 1;

	var html = "<div class = 'lnet form-group form-inline' data-nextid="+nextid+">";
	html += "<input type='text' name='localnets["+ourid+"][net]' class='form-control localnet network validate-ip' value='"+net+"'> / ";
	html += "<input type='text' name='localnets["+ourid+"][mask]' class='form-control localnet cidr validate-netmask' value='"+cidr+"'>";
	html += "</div>\n";

	last.after(html);
}

</script>
