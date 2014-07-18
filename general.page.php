<?php
// vim: set ai ts=4 sw=4 ft=phtml:
$localnets = $this->getConfig('localnets');
if (!$localnets) {
	$localnets = array();
}

$add_local_network_field = _("Add Local Network Field");
$submit_changes = _("Submit Changes");

?>
<form autocomplete="off" action="" method="post">
<input type="hidden" name="category" value="general">
<table width="690px"> <!-- FIXME: Fixed Width -->
  <tr>
	<td colspan="2"><h5><?php echo _("Security Settings") ?><hr></h5></td>
  </tr>
<?php
$anonhelp = _("Allowing Inbound Anonymous SIP calls means that you will allow any call coming in form an un-known IP source to be directed to the 'from-pstn' side of your dialplan. This is where inbound calls come in. Although FreePBX severely restricts access to the internal dialplan, allowing Anonymous SIP calls does introduced additional security risks. If you allow SIP URI dialing to your PBX or use services like ENUM, you will be required to set this to Yes for Inbound traffic to work. This is NOT an Asterisk sip.conf setting, it is used in the dialplan in conjuction with the Default Context. If that context is changed above to something custom this setting may be rendered useless as well as if 'Allow SIP Guests' is set to no.");

echo $this->radioset("allowanon", _("Allow Anonymous Inbound SIP Calls"), $anonhelp, array("Yes", "No"), $this->getConfig("allowanon"));
?>

  <tr>
	<td colspan="2"><h5><?php echo _("NAT Settings") ?><hr></h5></td>
  </tr>

  <tr class='lnet' data-nextid=1>
	<td>
	  <?php echo fpbx_label(_("Local Networks"), _("Local network settings in the form of ip/cidr or ip/netmask. For networks with more than 1 LAN subnets, use the Add Local Network Field button for more fields. Blank fields will be ignored.")); ?>
	</td>
	<td>
	  <input type="text" name="localnets[0][net]" class="localnet validate=ip" value="<?php echo $localnets[0]['net'] ?>"> /
	  <input type="text" name="localnets[0][mask]" class="netmask validate-netmask" value="<?php echo $localnets[0]['mask'] ?>">
	</td>
  </tr>

<?php
// Are there any MORE nets?
// Remove the first one that we've displayed
unset ($localnets[0]);

// Now loop through any more, if they exist.
foreach ($localnets as $id => $arr) {
	print "<tr class='lnet' data-nextid=".($id+1)."><td></td><td>";
	print "<input type='text' name='localnets[{$id}][net]' class='localnet validate-ip' value='{$arr['net']}''> / ";
	print "<input type='text' name='localnets[{$id}][mask]' class='localnet validate-netmask' value='{$arr['mask']}'>\n";
	print "</td></tr>\n";
}
?>

  <tr class="nat-settings" id="auto-configure-buttons">
	<td></td>
	<td><br \>
	  <input type="button" id="localnet-add" value="<?php echo $add_local_network_field ?>" class="nat-settings" />
	</td>
  </tr>

<?php
// RTP Settings
?>

  <tr>
	<td colspan=2> <h5><?php echo _("RTP Settings") ?><hr /></h5></td>
  </tr>
  <tr>
	<td><a href='#' class='info'><?php echo _("RTP Port Ranges") ?><span><?php echo _("The starting and ending RTP port range") ?></span></a></td>
	<td>
	  <?php echo _("Start").":" ?> <input type='text' size='5' name='rtpstart' class='validate-int' value='<?php echo $this->getConfig('rtpstart') ?>'>
	  <?php echo _("End").":" ?> <input type='text' size='5' name='rtpend' class='validate-int' value='<?php echo $this->getConfig('rtpend') ?>'>
	</td>
  </tr>

<?php
echo $this->radioset("rtpchecksums", _("RTP Checksums"), _("Whether to enable or disable UDP checksums on RTP traffic"), array("Yes", "No"), $this->getConfig("rtpchecksums"));
echo $this->radioset("strictrtp", _("Strict RTP"), _("This will drop RTP packets that do not come from the source of the RTP stream. It is unusual to turn this off"), array("Yes", "No"), $this->getConfig("strictrtp"));
echo $this->radioset("icesupport", _("ICE Support"), "", array("True", "False"), $this->getConfig("icesupport"));
?>

  <tr>
	<td colspan="2"><h5><?php echo _("Audio Codecs")?><hr></h5></td>
  </tr>
  <tr>
	<td valign='top'><a href="#" class="info"><?php echo _("Codecs")?><span><?php echo _("This is the default Codec setting for new Trunks and Extensions.")?></span></a></td>
	<td>
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
	</td>
  </tr>
  <tr>
	<td colspan="2"><h6><input name="Submit" type="submit" value="Submit"></h6></td>
  </tr>
</table><!-- end of table frm_sipsettings -->

</table>

<script type="text/javascript">
$(document).ready(function(){
	$("#localnet-add").click(function() { addLocalnet() });
});

function addLocalnet() {
	// We'd like a new one, please.
	var last = $(".lnet:last");
	var ourid = last.data('nextid');
	var nextid = ourid + 1;

	var html = "<tr class='lnet' data-nextid="+nextid+"><td></td><td>";
	html += "<input type='text' name='localnets["+ourid+"][net]' class='localnet validate-ip' value=''> / ";
	html += "<input type='text' name='localnets["+ourid+"][mask]' class='localnet validate-netmask' value=''>";
	html += "</td></tr>\n";

	last.after(html);
}

</script>
