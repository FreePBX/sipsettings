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

/* Field Values for type field */
define('NORMAL','0');
define('CODEC','1');
define('VIDEO_CODEC','2');
define('CUSTOM','9');

function sipsettings_hookGet_config($engine) {
	global $core_conf;

	switch($engine) {
		case "asterisk":
			if (isset($core_conf) && is_a($core_conf, "core_conf")) {
				$raw_settings = sipsettings_get(true);

				/* TODO: This is example concept code

				   The only real conflicts are codecs (mainly cause
					 it will look ugly. So we should strip those but
					 leave the rest. If we overrite it, oh well

				 */
				$idx = 0;
				foreach ($core_conf->_sip_general as $entry) {
					switch (strtolower($entry['key'])) {
						case 'allow':
						case 'disallow':
							unset($core_conf->_sip_general[$idx]);
						break;
					default:
						// do nothing
					}
					$idx++;
				}

				foreach ($raw_settings as $var) {
					switch ($var['type']) {
						case NORMAL:
							$interim_settings[$var['keyword']] = $var['data'];
						break;

						case CODEC:
						case VIDEO_CODEC:
							$codecs[$var['keyword']] = $var['data'];
						break;

						case CUSTOM:
							$sip_settings[] = array($var['keyword'], $var['data']);
						break;
					default:
						// Error should be above
					}
				}
				unset($raw_settings);

				/* Codecs First */
				$core_conf->addSipGeneral('disallow','all');
				foreach ($codecs as $codec => $enabled) {
					if ($enabled == '1') {
						$core_conf->addSipGeneral('allow',$codec);
					}
				}
				unset($codecs);

				/* next figure out what we need to write out (deal with things like nat combos, etc. */

				$nat_mode = $interim_settings['nat_mode'];
				foreach ($interim_settings as $key => $value) {
					switch ($key) {
						case 'nat_mode':
						break;
						case 'externhost_val':
							if ($nat_mode == 'externhost' && $key != '') {
								$sip_settings[] = array('externhost', $value);
							}
						break;
						case 'externrefresh':
							if ($nat_mode == 'externhost' && $key != '') {
								$sip_settings[] = array($key, $value);
							}
						break;
						case 'externip_val':
							if ($nat_mode == 'externip' && $key != '') {
								$sip_settings[] = array('externip', $value);
							}
						break;
						case 'sip_language':
							if ($key != '') {
								$sip_settings[] = array('language', $value);
							}
						break;
						default:
							if (substr($key,0,9) == "localnet_" && $value != '') {
								if ($nat_mode != 'public') {
									$seq = substr($key,9);
									$network = "$value/".$interim_settings["netmask_$seq"];
									$sip_settings[] = array('localnet', $network);
								}
							} else if (substr($key,0,8) == "netmask_") {
								// do nothing, handled above
							} else {
								$sip_settings[] = array($key, $value);
							}
						}
					}
					unset($interim_settings);
					freepbx_debug($sip_settings);
					foreach ($sip_settings as $entry) {
						if ($entry[1] != '') {
							$core_conf->addSipGeneral($entry[0],$entry[1]);
						}
					}
			}
		break;
	}

	return true;
}

function sipsettings_get($raw=false) {

	$sql = "SELECT `keyword`, `data`, `type`, `seq` FROM `sipsettings` ORDER BY `type`, `seq`";
	$raw_settings = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

	/* Just give the SQL table if more convenient (such as in hookGet_config */
	if ($raw) {
		return $raw_settings;
	}

	/* Initialize first, then replace with DB, to make sure we have defaults */

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

	foreach ($raw_settings as $var) {
		switch ($var['type']) {
			case NORMAL:
				$sip_settings[$var['keyword']]                 = $var['data'];
			break;

			case CODEC:
				$sip_settings['codecs'][$var['keyword']]       = $var['data'];
			break;

			case VIDEO_CODEC:
				$sip_settings['video_codecs'][$var['keyword']] = $var['data'];
			break;

			case CUSTOM:
				$sip_settings['sip_custom_key_'.$var['seq']]   = $var['keyword'];
				$sip_settings['sip_custom_val_'.$var['seq']]   = $var['data'];
			break;
		default:
			// Error should be above
		}
	}
	unset($raw_settings);

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
			case 'rtpholdtimeout':
				// validation: must be > $sip_settings['rtptimeout'] (and of course a proper number)
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
		$save_settings[] = array($db->escapeSimple($key),$db->escapeSimple($val),$seq++,VIDEO_CODEC); 
	}

	// TODO: shouldn't do DELETE/INSERT but for now ...
	//
	sql("DELETE FROM `sipsettings` WHERE 1");

	$compiled = $db->prepare('INSERT INTO sipsettings (keyword, data, seq, type) values (?,?,?,?)');
	$result = $db->executeMultiple($compiled,$save_settings);
	if(DB::IsError($result)) {
		die_freepbx($result->getDebugInfo()."<br><br>".'error adding to sipsettings table');	
	}
	return true;
}

?>
