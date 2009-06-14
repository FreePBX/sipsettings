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

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$dispnum = "sipsettings"; //used for switch on config.php

// TODO: Doesn't do anything yet, just initial screen mockup
//
switch ($action) {
	case "edit":  //just delete and re-add
	break;
}
?>

</div>

<div class="content">
<?php
	//TODO: Pull these out os sipsettings_get(). These are just to test the GUI right now.
?>
	<h2><?php echo _("Edit Settings"); ?></h2>
<?php

	// Determines how many columns per row for the codecs and formats the table
	//
	$cols_per_row = 4;
	$width = (100.0 / $cols_per_row);
	$tabindex = 0;

	$sip_settings = sipsettings_get();

	// General Settings
	$language = "";
	$srvlookup = "no";

	// Notification and MWI
	$checkmwi=10;
	$notifyringing = 'no';
	$notifyhold = 'no';

	// Audio Codecs
	$g726nonstandard = 'no';
	$t38pt_udptl = 'no';
	$codecs = array(
		'ulaw' => 'checked',
		'alaw' => 'checked',
		'slin' => 'checked',
		'g726' => '',
		'gsm' => 'checked',
		'g729' => '',
		'ilbc' => 'checked',
		'g723' => '',
		'g726aal2' => '',
		'adpcm' => '',
		'lpc10' => '',
		'speex' => '',
		'g722' => 'checked',
		'jpeg' => '',
		'png' => '',
		);

	// Video Codecs
	$videosupport = "checked";
	$maxcallbitrate=384;
	$video_codecs = array(
		'h261' => 'checked',
		'h263' => 'checked',
		'h263p' => 'checked',
		'h264' => '',
		);

	// NAT Settings
	$nat= "route";
	$nat_mode = "externhost";
	$externip_val = "";
	$externhost_val = "";
	$externrefresh = "60";

	// Media & RTP Settings
	$canreinvite = "no";
	$rtptimeout = "30";
	$rtpholdtimeout = "300";
	$rtpkeepalive = "";

	// Registration Settings
	$registertimeout=20;
	$registerattempts=10;
	$maxexpiry=3600;
	$minexpiry=60;
	$defaultexpiry=120;

	// Jitter Buffer Settings
	$jbenable = "yes";
	$jbforce = "no";
	$jbimpl = "fixed";
	$jbmaxsize = 200;
	$jbresyncthreshold = 1000;
	$jblog = "no";

	// Advanced Settings
	$context = "from-sip-external";
	$allowguest = "yes";
	$bindaddr="0.0.0.0";
	$bindport="5060";
	$contactdeny="";
	$contactpermit="";
	$t1min="100";

?>
	<form autocomplete="off" name="editSip" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return checkConf();">
	<input type="hidden" name="action" value="edit">
	<table width="560px">
	<tr>
		<td colspan="2"><h5><?php echo _("General Settings")?><hr></h5></td>
	</tr>
	<tr>
		<td>
			<a href="#" class="info"><?php echo _("Language")?><span><?php echo _("Default Language for a channel, Asterisk: language")?></span></a>
		</td>
		<td><input type="text" id="sip-language" name="sip-language" value="<?php echo $language ?>" tabindex="<?php echo ++$tabindex;?>"></td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("SRV Lookup")?><span><?php echo _("Enable Asterisk srvlookup. See current version of Asterisk for limitations on SRV functionality.")?></span></a>
		</td>
		<td> 
			<table width="100%">
				<tr>
					<td width="25%">
						<input id="srvlookup-yes" type="radio" name="srvlookup" value="yes" tabindex="<?php echo ++$tabindex;?>"<?php echo $srvlookup=="yes"?"checked=\"yes\"":""?>/>
						<label for="srvlookup-yes"><?php echo _("Enabled") ?></label>
					</td>

					<td width="25%">
						<input id="srvlookup-no" type="radio" name="srvlookup" value="no" tabindex="<?php echo ++$tabindex;?>"<?php echo $srvlookup=="no"?"checked=\"no\"":""?>/>
						<label for="srvlookup-no"><?php echo _("Disabled") ?></label>
					</td>

					<td width="25%"> </td><td width="25%"></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2"><h5><?php echo _("Notification & MWI")?><hr></h5></td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("MWI Polling Freq")?><span><?php echo _("Frequency in seconds to check if MWI state has changed and inform peers.")?></span></a>
		</td>
		<td><input type="text" size="3" id="checkmwi" name="checkmwi" value="<?php echo $checkmwi ?>" tabindex="<?php echo ++$tabindex;?>"></td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("Notify Ringing")?><span><?php echo _("Control whether subscriptions already INUSE get sent RINGING when another call is sent. Useful when using BLF.")?></span></a>
		</td>
		<td> 
			<table width="100%">
				<tr>
					<td width="25%">
						<input id="notifyringing-yes" type="radio" name="notifyringing" value="yes" tabindex="<?php echo ++$tabindex;?>"<?php echo $notifyringing=="yes"?"checked=\"yes\"":""?>/>
						<label for="notifyringing-yes"><?php echo _("Yes") ?></label>
					</td>

					<td width="25%">
						<input id="notifyringing-no" type="radio" name="notifyringing" value="no" tabindex="<?php echo ++$tabindex;?>"<?php echo $notifyringing=="no"?"checked=\"no\"":""?>/>
						<label for="notifyringing-no"><?php echo _("No") ?></label>
					</td>

					<td width="25%"> </td><td width="25%"></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("Notify Hold")?><span><?php echo _("Control whether subscriptions INUSE get sent ONHOLD when call is placed on hold. Useful when using BLF.")?></span></a>
		</td>
		<td> 
			<table width="100%">
				<tr>
					<td width="25%">
						<input id="notifyhold-yes" type="radio" name="notifyhold" value="yes" tabindex="<?php echo ++$tabindex;?>"<?php echo $notifyhold=="yes"?"checked=\"yes\"":""?>/>
						<label for="notifyhold-yes"><?php echo _("Yes") ?></label>
					</td>

					<td width="25%">
						<input id="notifyhold-no" type="radio" name="notifyhold" value="no" tabindex="<?php echo ++$tabindex;?>"<?php echo $notifyhold=="no"?"checked=\"no\"":""?>/>
						<label for="notifyhold-no"><?php echo _("No") ?></label>
					</td>

					<td width="25%"> </td><td width="25%"></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2"><h5><?php echo _("Audio Codecs")?><hr></h5></td>
	</tr>
	<tr>
		<td valign='top'><a href="#" class="info"><?php echo _("Codecs")?><span><?php echo _("Check the desired codecs, all others will be disabled unless explicitly enabled in a device or trunks configuration.")?></span></a></td>
		<td>
		<table width="100%">
			<tr>
<?php
	$cols = $cols_per_row;
	foreach ($codecs as $codec => $codec_state) {
		if ($cols == 0) {
			echo "</tr><tr>\n";
			$cols = $cols_per_row;
		}
		$cols--;
		$tabindex++;
		$codec_trans = _($codec);
		echo <<< END
				<td width="$width%">
					<input type="checkbox" value="1" name="$codec" id="$codec" class="audio-codecs" tabindex="$tabindex" $codec_state />
					<label for="$codec"> $codec_trans </label>
				</td>
END;
	}
?>
			</tr>
		</table>

		</td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("Non-Standard g726")?><span><?php echo _("Asterisk: g726nonstandard. If the peer negotiates G726-32 audio, use AAL2 packing order instead of RFC3551 packing order (this is required for Sipura and Grandstream ATAs, among others). This is contrary to the RFC3551 specification, the peer _should_ be negotiating AAL2-G726-32 instead.")?></span></a>
		</td>
		<td> 
			<table width="100%">
				<tr>
					<td width="25%">
						<input id="g726nonstandard-yes" type="radio" name="g726nonstandard" value="yes" tabindex="<?php echo ++$tabindex;?>"<?php echo $g726nonstandard=="yes"?"checked=\"yes\"":""?>/>
						<label for="g726nonstandard-yes"><?php echo _("Yes") ?></label>
					</td>

					<td width="25%">
						<input id="g726nonstandard-no" type="radio" name="g726nonstandard" value="no" tabindex="<?php echo ++$tabindex;?>"<?php echo $g726nonstandard=="no"?"checked=\"no\"":""?>/>
						<label for="g726nonstandard-no"><?php echo _("No") ?></label>
					</td>

					<td width="25%"> </td><td width="25%"></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("T38 Pass-Through")?><span><?php echo _("Asterisk: t38pt_udptl. Enables T38 passthrough if enabled. This SIP channels that support sending/receiving T38 Fax codecs to pass the call. Asterisk can not process the media.")?></span></a>
		</td>
		<td> 
			<table width="100%">
				<tr>
					<td width="25%">
						<input id="t38pt_udptl-yes" type="radio" name="t38pt_udptl" value="yes" tabindex="<?php echo ++$tabindex;?>"<?php echo $t38pt_udptl=="yes"?"checked=\"yes\"":""?>/>
						<label for="t38pt_udptl-yes"><?php echo _("Yes") ?></label>
					</td>

					<td width="25%">
						<input id="t38pt_udptl-no" type="radio" name="t38pt_udptl" value="no" tabindex="<?php echo ++$tabindex;?>"<?php echo $t38pt_udptl=="no"?"checked=\"no\"":""?>/>
						<label for="t38pt_udptl-no"><?php echo _("No") ?></label>
					</td>

					<td width="25%"> </td><td width="25%"></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2"><h5><?php echo _("Video Codecs")?><hr></h5></td>
	</tr>

	<tr>
		<td><a href="#" class="info"><?php echo _("Video Support")?><span><?php echo _("Check to enable and then choose allowed codecs.")?></span></a></td>
		<td>
		<table width="100%"><tr><td>
			<input type="checkbox" value="1" name="videosupport" id="videosupport" class="videosupport" tabindex="<?php echo ++$tabindex; ?>" <?php echo $videosupport ?> />
			<label id="videosupport" for="videosupport"><?php echo _("enable") ?></label>
		</tr></td></table>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
		<table width="100%">
			<tr>
<?php
	$cols = $cols_per_row;
	foreach ($video_codecs as $codec => $codec_state) {
		if ($cols == 0) {
			echo "</tr><tr>\n";
			$cols = $cols_per_row;
		}
		$cols--;
		$tabindex++;
		$codec_trans = _($codec);
		echo <<< END
				<td width="$width%">
					<input type="checkbox" value="1" name="$codec" id="$codec" class="video-codecs" tabindex="$tabindex" $codec_state />
					<label for="$codec"> $codec_trans </label>
				</td>
END;
	}
?>
			</tr>
		</table>

		</td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("Max Bit Rate")?><span><?php echo _("Maximum bitrate for video calls in kb/s")?></span></a>
		</td>
		<td><input type="text" size="3" id="maxcallbitrate" name="maxcallbitrate" class="video-codecs" value="<?php echo $maxcallbitrate ?>" tabindex="<?php echo ++$tabindex;?>"></td>
	</tr>

	<tr>
		<td colspan="2"><h5><?php echo _("NAT Settings") ?><hr></h5></td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("Nat")?><span><?php echo _("Asterisk nat setting:<br /> yes = Always ignore info and assume NAT<br /> no = Use NAT mode only according to RFC3581 <br /> never = Never attempt NAT mode or RFC3581 <br /> route = Assume NAT, don't send rport")?></span></a>
		</td>
		<td> 
			<table width="100%">
				<tr>
					<td width="25%">
						<input id="nat-yes" type="radio" name="nat" value="yes" tabindex="<?php echo ++$tabindex;?>"<?php echo $nat=="yes"?"checked=\"yes\"":""?>/>
						<label for="nat-yes">yes</label>
					</td>

					<td width="25%">
						<input id="nat-no" type="radio" name="nat" value="no" tabindex="<?php echo ++$tabindex;?>"<?php echo $nat=="no"?"checked=\"no\"":""?>/>
						<label for="nat-no">no</label>
					</td>

					<td width="25%">
						<input id="nat-never" type="radio" name="nat" value="never" tabindex="<?php echo ++$tabindex;?>"<?php echo $nat=="never"?"checked=\"never\"":""?>/>
						<label for="nat-never">never</label>
					</td>

					<td width="25%">
						<input id="nat-route" type="radio" name="nat" value="route" tabindex="<?php echo ++$tabindex;?>"<?php echo $nat=="route"?"checked=\"route\"":""?>/>
						<label for="nat-route">route</label>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("IP Configuration")?><span><?php echo _("Indicate whether the box has a public IP or requires NAT settings. Automatic onfiguration of what is often put in sip_nat.conf")?></span></a>
		</td>
		<td>
			<table width="100%">
				<tr>
					<td>
						<input id="nat-none" type="radio" name="nat_mode" value="none" tabindex="<?php echo ++$tabindex;?>"<?php echo $nat_mode=="none"?"checked=\"$nat_mode\"":""?>/>
						<label for="nat-none"><?php echo _("Public") ?></label>

						<input id="externip" type="radio" name="nat_mode" value="externip" tabindex="<?php echo ++$tabindex;?>"<?php echo $nat_mode=="externip"?"checked=\"$nat_mode\"":""?>/>
						<label for="externip"><?php echo _("Static") ?></label>

						<input id="externhost" type="radio" name="nat_mode" value="externhost" tabindex="<?php echo ++$tabindex;?>"<?php echo $nat_mode=="externhost"?"checked=\"$nat_mode\"":""?>/>
						<label for="externhost"><?php echo _("Dynamic") ?></label>
					</td>
					<td align="right">
						<input align="right" type="button" id="nat-auto-configure"  value="<?php echo _("Auto Configure")?>" class="nat-settings" />
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr class="nat-settings externip">
		<td><a href="#" class="info"><?php echo _("External IP")?><span><?php echo _("External Static IP or FQDN as seen on the WAN side of the router. (asterisk: externip)")?></span></a></td>
		<td><input type="text" id="externip_val" name="externip_val" value="<?php echo $externip_val ?>" tabindex="<?php echo ++$tabindex;?>"></td>
	</tr>

	<tr class="nat-settings externhost">
		<td>
			<a href="#" class="info"><?php echo _("Dynamic Host")?><span><?php echo _("External FQDN as seen on the WAN side of the router and updated dynamically, e.g. mydomain.dyndns.com. (asterisk: externhost)")?></span></a>
		</td>
		<td>
			<input type="text" id="externhost_val" name="externhost_val" size="34" value="<?php echo $externhost_val ?>" tabindex="<?php echo ++$tabindex;?>">
			<input type="text" id="externrefresh" name="externrefresh" size="3" value="<?php echo $externrefresh ?>" tabindex="<?php echo ++$tabindex;?>">
			<a href="#" class="info"><small><?php echo _("External Refresh")?><span><?php echo _("How often to refresh the External Host FQDN.")?></span></small></a>
		</td>
	</tr>
	<tr class="nat-settings">
		<td>
			<a href="#" class="info"><?php echo _("Local Neworks")?><span><?php echo _("Local network settings (Asterisk: localnet) in the form of ip/mask such as 192.168.1.0/255.255.255.0. For networks with more than 2 lan subnets, use the Additional SIP settings below to define them.")?></span></a>
		</td>
		<td>
			<input type="text" id="localnet-0" name="localnet-0"  value="<?php echo $localnet_0 ?>" tabindex="<?php echo ++$tabindex;?>"> /
			<input type="text" id="netmask-0" name="netmask-0" value="<?php echo $netmask_0 ?>" tabindex="<?php echo ++$tabindex;?>">
		</td>
	</tr>
	<tr class="nat-settings">
		<td>
		</td>
		<td>
			<input type="text" id="localnet-1" name="localnet-1"  value="<?php echo $localnet_1 ?>" tabindex="<?php echo ++$tabindex;?>"> /
			<input type="text" id="netmask-1" name="netmask-1" value="<?php echo $netmask_1 ?>" tabindex="<?php echo ++$tabindex;?>">
		</td>
	</tr>

	<tr>
		<td colspan="2"><h5><?php echo _("MEDIA & RTP Settings") ?><hr></h5></td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("Reinvite Behavior")?><span><?php echo _("Asterisk: canreinvite. yes: standard reinvites; no: never; nonat: An additional option is to allow media path redirection (reinvite) but only when the peer where the media is being sent is known to not be behind a NAT (as the RTP core can determine it based on the apparent IP address the media arrives from; update: use UPDATE for media path redirection, instead of INVITE. (yes = update + nonat)")?></span></a>
		</td>
		<td> 
			<table width="100%">
				<tr>
					<td width="25%">
						<input id="canreinvite-yes" type="radio" name="canreinvite" value="yes" tabindex="<?php echo ++$tabindex;?>"<?php echo $canreinvite=="yes"?"checked=\"yes\"":""?>/>
						<label for="canreinvite-yes"><?php echo _("Yes") ?></label>
					</td>

					<td width="25%">
						<input id="canreinvite-no" type="radio" name="canreinvite" value="no" tabindex="<?php echo ++$tabindex;?>"<?php echo $canreinvite=="no"?"checked=\"no\"":""?>/>
						<label for="canreinvite-no"><?php echo _("No") ?></label>
					</td>

					<td width="25%">
						<input id="canreinvite-nonat" type="radio" name="canreinvite" value="nonat" tabindex="<?php echo ++$tabindex;?>"<?php echo $canreinvite=="nonat"?"checked=\"nonat\"":""?>/>
						<label for="canreinvite-nonat">never</label>
					</td>

					<td width="25%">
						<input id="canreinvite-update" type="radio" name="canreinvite" value="update" tabindex="<?php echo ++$tabindex;?>"<?php echo $canreinvite=="update"?"checked=\"update\"":""?>/>
						<label for="canreinvite-update">update</label>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("RTP Timers")?><span><?php echo _("Asterisk: rtptimeout. Terminate call if rtptimeout seconds of no RTP or RTCP activity on the audio channel when we're not on hold. This is to be able to hangup a call in the case of a phone disappearing from the net, like a powerloss or grandma tripping over a cable.<br /> Asterisk: rtpholdtimeout. Terminate call if rtpholdtimeout seconds of no RTP or RTCP activity on the audio channel when we're on hold (must be > rtptimeout). <br /> Asterisk: rtpkeepalive. Send keepalives in the RTP stream to keep NAT open during periods where no RTP stream may be flowing (like on hold).")?></span></a>
		</td>
		<td>
				<input type="text" size="1" id="rtptimeout" name="rtptimeout" value="<?php echo $rtptimeout ?>" tabindex="<?php echo ++$tabindex;?>"><small>(rtptimeout)</small>&nbsp;
				<input type="text" size="1" id="rtpholdtimeout" name="rtpholdtimeout" value="<?php echo $rtpholdtimeout ?>" tabindex="<?php echo ++$tabindex;?>"><small>(rtpholdtimeout)</small>&nbsp;
				<input type="text" size="1" id="rtpkeepalive" name="rtpkeepalive" value="<?php echo $rtpkeepalive ?>" tabindex="<?php echo ++$tabindex;?>"><small>(rtpkeepalive)</small>
		</td>
	</tr>

	<tr>
		<td colspan="2"><h5><?php echo _("Registration Settings") ?><hr></h5></td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("Registration Attempts")?><span><?php echo _("Asterisk: registertimeout. Retry registration attempts every registertimeout seconds until successful or until registrationattempts tries have been made.<br /> Asterisk: registrationattempts. Number of times to try and register before giving up. A value of 0 means keep trying forever. Normally this should be set to 0 so that Asterisk will continue to register until successful in the case of network or gateway outagages.")?></span></a>
		</td>
		<td>
				<input type="text" size="2" id="registertimeout" name="registertimeout" value="<?php echo $registertimeout ?>" tabindex="<?php echo ++$tabindex;?>"><small>(registertimeout)</small>&nbsp;
				<input type="text" size="2" id="registerattempts" name="registerattempts" value="<?php echo $registerattempts ?>" tabindex="<?php echo ++$tabindex;?>"><small>(registerattempts)</small>&nbsp;
		</td>
	</tr>

	<tr>
		<td>
			<a href="#" class="info"><?php echo _("Registration Times")?><span><?php echo _("Asterisk: minexpiry. Minimum length of registrations/subscriptions.<br /> Asterisk: maxepiry. Maximum allowed time of incoming registrations<br /> Asterisk: defaultexpiry. Default length of incoming and outgoing registrations.")?></span></a>
		</td>
		<td>
				<input type="text" size="2" id="minexpiry" name="minexpiry" value="<?php echo $minexpiry ?>" tabindex="<?php echo ++$tabindex;?>"><small>(minexpiry)</small>&nbsp;
				<input type="text" size="2" id="maxexpiry" name="maxexpiry" value="<?php echo $maxexpiry ?>" tabindex="<?php echo ++$tabindex;?>"><small>(maxexpiry)</small>&nbsp;
				<input type="text" size="2" id="defaultexpiry" name="defaultexpiry" value="<?php echo $defaultexpiry ?>" tabindex="<?php echo ++$tabindex;?>"><small>(defaultexpiry)</small>
		</td>
	</tr>

<?/*
;------------------------------ JITTER BUFFER CONFIGURATION --------------------------
; jbenable = yes              ; Enables the use of a jitterbuffer on the receiving side of a
                              ; SIP channel. Defaults to "no". An enabled jitterbuffer will
                              ; be used only if the sending side can create and the receiving
                              ; side can not accept jitter. The SIP channel can accept jitter,
                              ; thus a jitterbuffer on the receive SIP side will be used only
                              ; if it is forced and enabled.

; jbforce = no                ; Forces the use of a jitterbuffer on the receive side of a SIP
                              ; channel. Defaults to "no".

; jbmaxsize = 200             ; Max length of the jitterbuffer in milliseconds.

; jbresyncthreshold = 1000    ; Jump in the frame timestamps over which the jitterbuffer is
                              ; resynchronized. Useful to improve the quality of the voice, with
                              ; big jumps in/broken timestamps, usually sent from exotic devices
                              ; and programs. Defaults to 1000.

; jbimpl = fixed              ; Jitterbuffer implementation, used on the receiving side of a SIP
                              ; channel. Two implementations are currently available - "fixed"
                              ; (with size always equals to jbmaxsize) and "adaptive" (with
                              ; variable size, actually the new jb of IAX2). Defaults to fixed.

; jblog = no                  ; Enables jitterbuffer frame logging. Defaults to "no".
;-----------------------------------------------------------------------------------
*/?>
	<tr>
		<td colspan="2"><h5><?php echo _("Jitter Buffer Settings") ?><hr></h5></td>
	</tr>

<?/*
;-------------- ADVANCED -----------
context=default                 ; Default context for incoming calls
bindaddr=0.0.0.0                ; IP address to bind to (0.0.0.0 binds to all)
;allowguest=no                  ; Allow or reject guest calls (default is yes)

bindport=5060                   ; UDP Port to bind to (SIP standard port is 5060)
                                ; bindport is the local UDP port that Asterisk will listen on
;t1min=100                      ; Minimum roundtrip time for messages to monitored hosts

;contactdeny=0.0.0.0/0.0.0.0           ; Use contactpermit and contactdeny to
;contactpermit=172.16.0.0/255.255.0.0  ; restrict at what IPs your users may
                                       ; register their phones.

TODO: Add generic textarea for freeform addition of more obscure settings 
TODO: if data.count > 2 then dynamically add more localnet or put in text area (js below)
*/?>
	<tr>
		<td colspan="2"><h5><?php echo _("Advanced General Settings") ?><hr></h5></td>
	</tr>

	<tr>
		<td colspan="2"><br><h6><input name="Submit" type="submit" value="<?php echo _("Submit Changes")?>" tabindex="<?php echo ++$tabindex;?>"></h6></td>		
	</tr>
	</table>
<script language="javascript">
<!--
$(document).ready(function(){

	$.ajaxTimeout( 10000 );
	$("#nat-auto-configure").click(function(){

		$.ajax({
			type: 'POST',
			url: "<?php echo $_SERVER["PHP_SELF"]; ?>",
			data: "quietmode=1&skip_astman=1&handler=file&module=sipsettings&file=natget.html.php",
			dataType: 'json',
			success: function(data) {
				//alert("Got a Response for");
				if (data.status == 'success') {
					$('#externip_val').attr("value",data.externip);
					$('#externhost_val').attr("value",data.externhost);
					$('#localnet-0').attr("value",data.localnet_0);
					$('#localnet-1').attr("value",data.localnet_1);
					$('#netmask-0').attr("value",data.netmask_0);
					$('#netmask-1').attr("value",data.netmask_1);

					// TODO: if data.count > 2 then dynamically add more or put in text area

				} else {
					alert(data.status);
				}
			},
			error: function(data) {
				alert("<?php echo _("An Error occured trying to fetch Bandwidth.com trunk information")?>");
			},
		});

		return false;
	});

	$(".video-codecs").attr("disabled",!$("#videosupport").attr("checked"));

	if (document.getElementById("externhost").checked) {
		$(".externip").hide();
	} else if (document.getElementById("externip").checked) {
		$(".externhost").hide();
	} else {
		$(".nat-settings").hide();
	}

	$("#videosupport").click(function(){
		$(".video-codecs").attr("disabled",!this.checked);
	});

	$("#nat-none").click(function(){
		$(".nat-settings").hide();
	});
	$("#externip").click(function(){
		$(".nat-settings").show();
		$(".externhost").hide();
	});
	$("#externhost").click(function(){
		$(".nat-settings").show();
		$(".externip").hide();
	});
});

var theForm = document.editSip;
theForm.name.focus();

function checkConf()
{
	return true;
}

//-->
</script>
	</form>
<?php		
?>
