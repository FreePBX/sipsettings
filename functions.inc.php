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

// Use hookGet_config so that everyone (like core) will have written their
// SIP settings and then we can remove any that we are going to override
//

// Field Values for type field
// NORMAL = 0
// CODEC =  1
// CUSTOM = 2
define('NORMAL','0');
define('CODEC','1');
define('CUSTOM','2');

function sipsettings_hookGet_config($engine) {
	global $ext;
	switch($engine) {
		case "asterisk":
		break;
	}

	return true;
}

function sipsettings_get() {

	/* TODO: Initialize all these settings as here and then query them
	 *       from the DB that way we get default values in case they
	 *       are not set in the db or missing.
	 */

	$sip_settings['nat']               = 'yes';
	$sip_settings['nat_mode']          = 'externip';
	$sip_settings['externip_val']      = '';
	$sip_settings['externhost_val']    = '';
	$sip_settings['externrefresh']     = '120';
	$sip_settings['localnet_0']        = '';
	$sip_settings['netmask_0']         = '255.255.255.0';

	$sip_settings['codecs']            =  array(
		'ulaw'     => '1',
		'alaw'     => '1',
		'slin'     => '',
		'g726'     => '',
		'gsm'      => '1',
		'g729'     => '',
		'ilbc'     => '',
		'g723'     => '',
		'g726aal2' => '',
		'adpcm'    => '',
		'lpc10'    => '',
		'speex'    => '',
		'g722'     => '',
		'jpeg'     => '',
		'png'      => '',
		);

	$sip_settings['g726nonstandard']   = 'no';
	$sip_settings['t38pt_udptl']       = 'no';

	$sip_settings['video_codecs']      = array(
		'h261'  => '',
		'h263'  => '',
		'h263p' => '',
		'h264'  => '',
		);

	$sip_settings['videosupport']      = 'no';
	$sip_settings['maxcallbitrate']    = '384';

	$sip_settings['canreinvite']       = 'no';
	$sip_settings['rtptimeout']        = '30';
	$sip_settings['rtpholdtimeout']    = '300';
	$sip_settings['rtpkeepalive']      = '';

	$sip_settings['checkmwi']          = '';
	$sip_settings['notifyringing']     = 'no';
	$sip_settings['notifyhold']        = 'no';

	$sip_settings['registertimeout']   = '20';
	$sip_settings['registerattempts']  = '0';
	$sip_settings['maxexpiry']         = '3600';
	$sip_settings['minexpiry']         = '60';
	$sip_settings['defaultexpiry']     = '120';

	$sip_settings['jbenable']          = 'no';
	$sip_settings['jbforce']           = 'no';
	$sip_settings['jpimpl']            = 'fixed';
	$sip_settings['jbmaxsize']         = '200';
	$sip_settings['jbresyncthreshold'] = '1000';
	$sip_settings['jblog']             = 'no';

	$sip_settings['sip_language']      = '';
	$sip_settings['context']           = 'from-sip-external';
	$sip_settings['bindaddr']          = '0.0.0.0';
	$sip_settings['bindport']          = '5060';
	$sip_settings['allowguest']        = 'yes';
	$sip_settings['srvlookup']         = 'no';

	$sip_settings['sip_custom_key_0']  = '';
	$sip_settings['sip_custom_val_0']  = '';

	// TODO: Query from the DB now and reset where it has chnaged

	return $sip_settings;
}

// Add a sipsettings
function sipsettings_edit($sip_settings) {
	global $db;
	$save_settings = array();

	$codecs = $sip_settings['codecs'];
	$video_codecs = $sip_settings['video_codecs'];
	unset($sip_settings['codecs']);
	unset($sip_settings['video_codecs']);

	// TODO: this is where I will build validation before saving
	//
	foreach ($sip_settings as $key => $val) {
		switch ($key) {
			case 'bindaddr':
				// ip validate this and store
				$save_settings[] = array($key,$db->escapeSimple($val),'0',NORMAL); 
			break;
		default:
			if (substr($key,0,9) == "localnet_") {
				// ip validate this and store
				$seq = substr($key,9);
				$save_settings[] = array($key,$db->escapeSimple($val),$seq,NORMAL); 
			} else if (substr($key,0,8) == "netmask_") {
				// ip validate this and store
				$seq = substr($key,8);
				$save_settings[] = array($key,$db->escapeSimple($val),$seq,NORMAL); 
			} else if (substr($key,0,15) == "sip_custom_key_") {
				$seq = substr($key,15);
				$save_settings[] = array($db->escapeSimple($val),$db->escapeSimple($sip_settings["sip_custom_val_$seq"]),$seq,CUSTOM); 
			} else if (substr($key,0,15) == "sip_custom_val_") {
				// skip it, we will seek it out when we see the sip_custom_key
			} else {
				$save_settings[] = array($key,$val,'0',NORMAL); 
			}
		}
	}
	$seq = 0;
	foreach ($codecs as $key => $val) {
		$save_settings[] = array($db->escapeSimple($key),$db->escapeSimple($val),$seq++,CODEC); 
	}
	$seq = 0;
	foreach ($video_codecs as $key => $val) {
		$save_settings[] = array($db->escapeSimple($key),$db->escapeSimple($val),$seq++,CODEC); 
	}

	// TODO: shouldn't do DELETE/INSERT but for now ...
	//
	sql("DELETE FROM `sipsettings` WHERE 1");

	$compiled = $db->prepare('INSERT INTO sipsettings (keyword, data, seq, type) values (?,?,?,?)');
	$result = $db->executeMultiple($compiled,$save_settings);
	if(DB::IsError($result)) {
		die_freepbx($result->getDebugInfo()."<br><br>".'error adding to sipsettings table');	
	}
	freepbx_debug($save_settings);
}

?>
