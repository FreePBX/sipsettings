<?php
// vim: set ai ts=4 sw=4 ft=php:
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

global $db;
global $amp_conf;
global $version;

$sql = <<< END
	CREATE TABLE IF NOT EXISTS `sipsettings` (
		`keyword` VARCHAR (50) NOT NULL default '',
		`data`    VARCHAR (255) NOT NULL default '',
		`seq`     TINYINT (1),
		`type`    TINYINT (1) NOT NULL default '0',
		PRIMARY KEY (`keyword`,`seq`,`type`)
	)
END;

$ss = FreePBX::create()->Sipsettings;

outn(_("checking for sipsettings table.."));
$tsql = "SELECT * FROM `sipsettings` limit 1";
$check = $db->getRow($tsql, DB_FETCHMODE_ASSOC);

// Figure out if we're using asterisk 11 or 12.
$version = FreePBX::Config()->get('ASTVERSION');
if (!empty($version)) {
	// Woo, we have a version
	if (version_compare($version, "12.2.0", ">=")) {
		$haspjsip = true;
	} else {
		$haspjsip = false;
	}
} else {
	// Well. I don't know what version of Asterisk I'm running.
	// Assume less than 12.
	$haspjsip = false;
}

if ($haspjsip) {
	$pjsip_port = 5060;
	$pjsiptls_port = 5061;
	$chansip_port = 5160;
	$chansiptls_port = 5161;
} else {
	$pjsip_port = 5160;
	$pjsiptls_port = 5161;
	$chansip_port = 5060;
	$chansiptls_port = 5061;
}

if(DB::IsError($check)) {
	out(_("none, creating table"));
	// table does not exist, create it
	sql($sql);

	$brand = FreePBX::Config()->get('DASHBOARD_FREEPBX_BRAND');
	$nt = notifications::create();
	$nt->add_notice('sipsettings', 'BINDPORT', sprintf(_("Default bind port for CHAN_PJSIP is: %s, CHAN_SIP is: %s"), $pjsip_port, $chansip_port), sprintf(_("The default bind ports for %s have changed. Please keep this is mind while configuring your devices. You can change this in SIP Settings. CHAN_PJSIP is: %s, CHAN_SIP is: %s"), $brand, $pjsip_port, $chansip_port), "http://wiki.freepbx.org/display/HTGS/CHAN_PJSIP+vs+CHAN_SIP", true, true);

	outn(_("populating default codecs.."));
	$sip_settings =  array(
		array('ulaw'    ,'1', '0', '1'),
		array('alaw'    ,'2', '1', '1'),
		array('slin'    ,'' , '2', '1'),
		array('gsm'     ,'3', '3', '1'),
		array('g726'    ,'4', '4', '1'),
		array('g729'    ,'' , '5', '1'),
		array('ilbc'    ,'' , '6', '1'),
		array('g723'    ,'' , '7', '1'),
		array('g726aal2','' , '8', '1'),
		array('adpcm'   ,'' , '9', '1'),
		array('lpc10'   ,'' ,'10', '1'),
		array('speex'   ,'' ,'11', '1'),
		array('g722'    ,'' ,'12', '1'),
		array('bindport',$chansip_port, '1', '0'),
		array('tlsbindport',$chansiptls_port, '1', '0'),
	);

	// Now insert minimal codec rows
	$compiled = $db->prepare("INSERT INTO sipsettings (keyword, data, seq, type) values (?,?,?,?)");
	$result = $db->executeMultiple($compiled,$sip_settings);
	if(DB::IsError($result)) {
		out(_("fatal error occurred populating defaults, check module"));
	} else {
		out(_("ulaw, alaw, gsm, g726 added"));
	}

	if ($haspjsip) {
		FreePBX::create()->Config->set_conf_values(array('ASTSIPDRIVER' => 'both'), true, true);
	} else {
		FreePBX::create()->Config->set_conf_values(array('ASTSIPDRIVER' => 'chansip'), true, true);
	}

	$ss->setConfig("udpport-0.0.0.0", $pjsip_port);
	$ss->setConfig("tcpport-0.0.0.0", $pjsip_port);
	$ss->setConfig("tlsport-0.0.0.0", $pjsiptls_port);
	$ss->setConfig("binds", array("udp" => array("0.0.0.0" => "on")));
} else {
	out(_("already exists"));
}

//OK let's do some migrating for BMO
$ss = FreePBX::Sipsettings();
if(!$ss->getConfig('rtpstart') || !$ss->getConfig('rtpend')) {
	out(_("Migrate rtp.conf values if needed and initialize"));

	$sql = "SELECT data FROM sipsettings WHERE keyword = 'rtpstart'";
	$rtpstart = sql($sql,'getOne');
	if (!$rtpstart) {
		$sql = "SELECT value FROM admin WHERE variable = 'RTPSTART'";
		$rtpstart = sql($sql,'getOne');
		if ($rtpstart) {
			out(sprintf(_("saving previous value of %s"), $rtpstart));
		}
	}
	if ($rtpstart) {
		out(_('Migrating rtpstart Setting from Old Format to BMO Object'));
		$ss->setConfig('rtpstart',$rtpstart);
	}

	$sql = "SELECT data FROM sipsettings WHERE keyword = 'rtpend'";
	$rtpend = sql($sql,'getOne');
	if (!$rtpend) {
		$sql = "SELECT value FROM admin WHERE variable = 'RTPEND'";
		$rtpend = sql($sql,'getOne');
		if ($rtpend) {
			out(sprintf(_("saving previous value of %s"), $rtpend));
		}
	}
	if ($rtpend) {
		out(_('Migrating rtpend Setting from Old Format to BMO Object'));
		$ss->setConfig('rtpend',$rtpend);
	}
}
// One way or another we've converted so we remove the interim variable from admin && sipsettings
sql("DELETE FROM admin WHERE variable IN ('RTPSTART', 'RTPEND')");
sql("DELETE FROM sipsettings WHERE keyword IN ('rtpstart', 'rtpend')");

//attempt to migrate all old localnets && netmasks
if(!$ss->getConfig('localnets')) {
	$localnetworks = array();
	$sql = "SELECT * from sipsettings where keyword LIKE 'localnet_%'";
	$localnets = sql($sql,'getAll',DB_FETCHMODE_ASSOC);
	foreach($localnets as $nets) {
		$break = explode("_",$nets['keyword']);
		$localnetworks[$break[1]]['net'] = $nets['data'];
	}
	$sql = "SELECT * from sipsettings where keyword LIKE 'netmask_%'";
	$netmasks = sql($sql,'getAll',DB_FETCHMODE_ASSOC);
	foreach($netmasks as $nets) {
		$break = explode("_",$nets['keyword']);
		$localnetworks[$break[1]]['mask'] = (preg_match('/\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b/',$nets['data'])) ? $ss->mask2cidr($nets['data']) : $nets['data'];
	}

	if(!empty($localnetworks)) {
		out(_('Migrating LocalNets and Netmasks'));
		$ss->setConfig('localnets',$localnetworks);
	}
}
sql("DELETE FROM sipsettings WHERE keyword LIKE 'netmask_%'");
sql("DELETE FROM sipsettings WHERE keyword LIKE 'localnet_%'");

// Move the old chan_sip externip into the general setting
$sql = "SELECT * from sipsettings where keyword='externip_val'";
$extip = sql($sql,'getAll',DB_FETCHMODE_ASSOC);
if (isset($extip[0])) {
	// If it's unset, overwrite it. If it's set, DON'T clobber it
	// as this is likely to be an upgrade after pjsip was added.
	if (!$ss->getConfig('externip')) {
		$ss->setConfig('externip', $extip[0]['data']);
	}
}

$sql = "SELECT * from sipsettings where keyword='tlsbindport'";
$tlsbp = sql($sql,'getAll',DB_FETCHMODE_ASSOC);
if (!isset($tlsbp)) {
	print_r("run");
	$sip_settings =  array(
		array('tlsbindport',$chansiptls_port, '1', '0'),
	);

	// Now insert minimal codec rows
	$compiled = $db->prepare("INSERT INTO sipsettings (keyword, data, seq, type) values (?,?,?,?)");
	$result = $db->executeMultiple($compiled,$sip_settings);

	if (!$ss->getConfig('tlsport-0.0.0.0')) {
		$ss->setConfig("tlsport-0.0.0.0", $pjsiptls_port);
	}
}

//attempt to migrate audio codecc
if(!$ss->getConfig('voicecodecs')) {
	$sql = "SELECT keyword from sipsettings where type = 1 AND data != '' order by seq";
	$codecs = sql($sql,'getAll',DB_FETCHMODE_ASSOC);
	// Just in case they don't turn on ANY codecs..
	$codecsValid = false;
	$seq = 1;
	foreach ($codecs as $c) {
		$newcodecs[$c['keyword']] = $seq++;
		$codecsValid = true;
	}
	if ($codecsValid) {
		out(_('Migrating Audio Codecs'));
		$ss->setConfig("voicecodecs", $newcodecs);
	} else {
		// They turned off ALL the codecs. Set them back to default.
		$ss->setConfig("voicecodecs", $ss->FreePBX->Codecs->getAudio(true));
	}
}
sql("DELETE FROM sipsettings WHERE type = 1");
//attempt to migrate video codecs
if(!$ss->getConfig('videocodecs')) {
	$sql = "SELECT keyword from sipsettings where type = 2 AND data != '' order by seq";
	$codecs = sql($sql,'getAll',DB_FETCHMODE_ASSOC);
	// Just in case they don't turn on ANY codecs..
	$codecsValid = false;
	$seq = 1;
	foreach ($codecs as $c) {
		$newcodecs[$c['keyword']] = $seq++;
		$codecsValid = true;
	}
	if ($codecsValid) {
		out(_('Migrating Video Codecs'));
		$ss->setConfig("videocodecs", $newcodecs);
	} else {
		// They turned off ALL the codecs. Set them back to default.
		$ss->setConfig("videocodecs", $ss->FreePBX->Codecs->getVideo(true));
	}
}
sql("DELETE FROM sipsettings WHERE type = 2");


if(!$ss->getConfig("allowanon")) {
	$sql = "SELECT `data` FROM `admin` WHERE `variable` = 'ALLOW_SIP_ANON'";
	$aa = sql($sql,'getOne');
	$aa = (!empty($aa) && $aa == 'Yes') ? $aa : 'No';
	$ss->setConfig("allowanon",$aa);
}
sql("DELETE FROM admin WHERE variable = 'ALLOW_SIP_ANON'");

/* Convert language to custom field */
$sql = "SELECT MAX(seq) FROM sipsettings WHERE type = 9";
$seq = sql($sql,'getOne');
$sql = "UPDATE sipsettings SET keyword = 'language', type = 9, seq = " . ($seq !== NULL ? $seq + 1 : 0) . " WHERE keyword = 'sip_language'";
sql($sql);


$sql = "UPDATE `sipsettings` SET `type` = 0 WHERE `keyword` = 'tcpenable'";
$sth = FREEPBX::Database()->prepare($sql);
try {
	$sth->execute();
} catch(\Exception $e) {
	$sql = "DELETE FROM `sipsettings` WHERE `type` != 0 AND `keyword` = 'tcpenable'";
	$sth = FREEPBX::Database()->prepare($sql);
	$sth->execute();
}

$sql = "DELETE FROM `sipsettings` WHERE `keyword` = 'enabletcp'";
sql($sql);
