<?php 

$codecs =  $this->getConfig('codecs');

if (!$codecs) {
	$allcodecs = $this->FreePBX->Codecs->getAll();
	$codecs = $allcodecs['audio'];
}

$localnets = $this->getConfig('localnets');
if (!$localnets) {
	$localnets[0]['net'] = "1.2.3.4";
	$localnets[0]['mask'] = "24";
	$localnets[1]['net'] = "2.3.4.5";
	$localnets[1]['mask'] = "24";
}

$add_local_network_field = _("Add Local Network Field");
$submit_changes = _("Submit Changes");

?>
<form autocomplete="off" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" name="category" value="general">
<table width="690px"> <!-- FIXME: Fixed Width -->
  <tr>
    <td colspan="2"><h5><?php echo _("NAT Settings") ?><hr></h5></td>
  </tr>

  <tr class='localnets' data-nextid=1>
    <td>
      <a href="#" class="info"><?php echo _("Local Networks")?><span><?php echo _("Local network settings in the form of ip/cidr such as 192.168.1.0/24. For networks with more 1 lan subnets, use the Add Local Network Field button for more fields. Blank fields will be removed upon submitting.")?></span></a>
    </td>
    <td>
      <input type="text" id="localnet_0_net" name="localnet_0_net" class="localnet validate=ip" value="<?php echo $localnets[0]['net'] ?>" tabindex="<?php echo ++$tabindex;?>"> /
      <input type="text" id="localnet_0_mask" name="localnet_0_mask" class="netmask validate-netmask" value="<?php echo $localnets[0]['mask'] ?>" tabindex="<?php echo ++$tabindex;?>">
    </td>
  </tr>

<?php
// Are there any MORE nets?
// Remove the first one that we've displayed
unset ($localnets[0]);

// Now loop through any more, if they exist.
foreach ($localnets as $id => $arr) {
	print "<tr class='localnets' data-nextid=".($id+1)."><td></td><td>";
	print "<input type='text' name='localnet_{$id}_net' class='localnet validate-ip' value='{$arr['net']}' tabindex='FIXME'> / \n";
	print "<input type='text' name='localnet_{$id}_mask' class='localnet validate-netmask' value='{$arr['mask']}' tabindex='FIXME'>\n";
	print "</td></tr>\n";
}
?>

  <tr class="nat-settings" id="auto-configure-buttons">
    <td></td>
    <td><br \>
      <input type="button" id="localnet-add" value="<?php echo $add_local_network_field ?>" class="nat-settings" />
    </td>
  </tr>

<tr><td colspan=2>
<h5><?php echo _("RTP Settings") ?><hr /></h5>
<p> 
<a href='#' class='info'><?php echo _("RTP Port Ranges") ?><span><?php echo _("The starting and ending RTP port range") ?></span></a>
    <?php echo _("Start:") ?> <input type='text' size='5' name='rtpstart' class='validate-int' value='<?php echo $this->getConfig('rtpstart') ?>' tabindex="<?php echo ++$tabindex ?>">
    <?php echo _("End:") ?> <input type='text' size='5' name='rtpend' class='validate-int' value='<?php echo $this->getConfig('rtpend') ?>' tabindex="<?php echo ++$tabindex ?>">
</p>

<p>RTP Checksums</p> <p>Strict RTP</p> <p>ICE Support</p> <p>STUN Server</p> <p>TURN server + User + Password</p>
</td></tr>

  <tr>
    <td colspan="2"><h5><?php echo _("Audio Codecs")?><hr></h5></td>
  </tr>
  <tr>
    <td valign='top'><a href="#" class="info"><?php echo _("Codecs")?><span><?php echo _("This is the default Codec setting for new Trunks and Extensions.")?></span></a></td>
    <td>
<?php
echo '<ul class="sortable">';
foreach ($codecs as $codec => $codec_state) {
    $tabindex++;
    $codec_trans = _($codec);
    $codec_checked = $codec_state ? 'checked' : '';
        echo '<li><a href="#">'
                . '<img src="assets/sipsettings/images/arrow_up_down.png" height="16" width="16" border="0" alt="move" style="float:none; margin-left:-6px; margin-bottom:-3px;cursor:move" /> '
                . '<input type="checkbox" '
                . ($codec_checked ? 'value="'. $seq++ . '" ' : '')
                . 'name="codec[' . $codec . ']" '
                . 'id="'. $codec . '" '
                . 'class="audio-codecs" tabindex="' . $tabindex. '" '
                . $codec_checked
                . ' />'
                . '<label for="'. $codec . '"> '
                . '<small>' . $codec_trans . '</small>'
                . ' </label></a></li>';
  }
echo '</ul>';

?>
    </td>
  </tr>
</table>

<script type="text/javascript">
$(document).ready(function(){
	$("#localnet-add").click(function() { addLocalnet() });
});

function addLocalnet() {
	// We'd like a new one, please.
	var last = $(".localnets:last");
	var ourid = last.data('nextid');
	var nextid = ourid + 1;

	var html = "<tr class='localnets' data-nextid="+nextid+"><td></td><td>";
	html += "<input type='text' name='localnet_"+ourid+"_net' class='localnet validate-ip' value=''> / ";
	html += "<input type='text' name='localnet_"+ourid+"_mask' class='localnet validate-netmask' value=''>";
	html += "</td></tr>\n";

	last.after(html);
}

</script>

