<?php
// vim: set ai ts=4 sw=4 ft=php:

class Sipsettings extends FreePBX_Helpers implements BMO {

	private $pagename = null;

	public static $dbDefaults = array(
		"rtpstart" => "10000", "rtpend" => "20000",
		"protocols" => array("udp", "tcp", "ws"),
		"rtpchecksums" => "Yes",
		"icesupport" => "False",
		"strictrtp" => "Yes",
		"allowguest" => "no",
		"allowanon" => "No",
		"showadvanced" => "no",
		"tcpport-0.0.0.0" => "5061", // Defaults, only used if this is an upgrade
		"udpport-0.0.0.0" => "5061",
	);

	public static function myDialplanHooks() {
		// Yes, we want to hook into dialplan generation,
		// and we don't care where.
		return true;
		// When we define this, you also need to create a function
		// 'doDialplanHook()', to actually do the hooking.
	}

	public function __construct($freepbx) {
		$this->FreePBX = $freepbx;
	}

	public function doConfigPageInit($display) {
		// Process POST/GET here
		//
		// What page are we showing?
		if (isset($_REQUEST['category'])) {
			if ($_REQUEST['category'] == "chansip") {
				$this->pagename = "chansip";
			} elseif ($_REQUEST['category'] == "pjsip") {
				$this->pagename = "pjsip";
				$this->doGeneralPost();
			} elseif ($_REQUEST['category'] == "general") {
				$this->doGeneralPost();
			} else {
				// Unknown pagename?
				// thow new Exception("WTF. You suck");
			}
		}
	}

	public function getRnav() { 

		$driver = $this->FreePBX->Config->get_conf_setting('ASTSIPDRIVER');
		$pages['general'] = _("General SIP Settings");

		if ($driver == "chan_sip" || $driver == "both") {
			$pages['chansip'] = "Chan SIP";
		}

		if ($driver == "chan_pjsip" || $driver == "both") {
			$pages['pjsip'] = "Chan PJSIP";
		}

		$str =  "<div class='rnav'><ul>";

		foreach ($pages as $k => $v) {
			if ($this->pagename == $k) {
				$id = "id='current'";
				$v = $v." (A)";
			} else {
				$id = "";
			}
			$str .= "<li $id><a href='config.php?display=sipsettings&category=$k'>$v</a></li>\n";
		}
		$str .= "</ul> </div>";
		return $str;
	}

	public function getActiveModules() {

		$driver = $this->FreePBX->Config->get_conf_setting('ASTSIPDRIVER');

		if ($driver == "both") {
			$str = "Asterisk is currently using <strong>chan_sip AND chan_pjsip</strong> for SIP Traffic.<br />You can change this on the Advanced Settings Page<br />\n";
		} else {
			$str = "Asterisk is currently using <strong>$driver</strong> for SIP Traffic.<br />You can change this on the Advanced Settings Page<br />\n";
		}

		return $str;
	}

	public function myShowPage() {
		if (!$this->pagename) {
			include 'general.page.php';
		} elseif ($this->pagename == "chansip") {
			include 'chansip.page.php';
		} elseif ($this->pagename == "pjsip") {
			include 'chanpj.page.php';
		} else {
			return "I DON'T KNOW\n";
		}
	}

	public function doGeneralPost() {
		if (!isset($_REQUEST['Submit']))
			return;

		// Codecs
		if (isset($_REQUEST['voicecodecs'])) {
			// Go through all the codecs that were handed back to
			// us, and create a new array with what they want.
			// Note we trust the browser to return the array in the correct
			// order here.
			$codecs = array_keys($_REQUEST['voicecodecs']);

			// Just in case they don't turn on ANY codecs..
			$codecsValid = false;

			$seq = 1;
			foreach ($codecs as $c) {
				$newcodecs[$c] = $seq++;
				$codecsValid = true;
			}

			if ($codecsValid) {
				$this->setConfig("voicecodecs", $newcodecs);
			} else {
				// They turned off ALL the codecs. Set them back to default.
				$this->setConfig("voicecodecs", $this->FreePBX->Codecs->getAudio(true));
			}

			// Finished. Unset it, and continue on.
			unset($_REQUEST['voicecodecs']);
		}

		// Ignore empty/invalid localnet settings
		if (isset($_REQUEST['localnets'])) {
			foreach ($_REQUEST['localnets'] as $i => $arr) {
				if (empty($arr['net']) || empty($arr['mask'])) {
					unset($_REQUEST['localnets'][$i]);
				}
			}
		}

		// This is in Request_Helper.class.php
		$ignored = $this->importRequest(null, "/(.+)bindip-(.+)$/");

		// There may be binds that matched..
		foreach ($ignored as $key => $var) {
			if (preg_match("/(.+)bindip-(.+)$/", $key, $match)) {
				$ip = str_replace("_", ".", $match[2]);
				$binds[$match[1]][$ip] = "on";
				continue;  // Don't save them
			}
		}

		if ($binds) {
			$this->setConfig("binds", $binds);
		}

		needreload();
	}

	private function radioset($id, $name, $help = "", $values, $current) {
		$out =  "<tr><td><a class='info'>$name<span>$help</span></a></td>\n";
		$out .= "<td><span class='radioset'>\n";
		foreach ($values as $v) {
			$out .= "<input id='$id-$v' name='$id' value='$v' type='radio'";
			if ($current === $v) {
				$out .= " checked";
			}
			$out .= "><label for='$id-$v'>$v</label>\n";
		}
		$out .= "</span></td></tr>\n";

		return $out;
	}

	public function genConfig() {

		// RTP Configuration
		$ss = $this->FreePBX->Sipsettings;
		$ssvars = array("rtpstart", "rtpend", "rtpchecksums", "dtmftimeout", "icesupport", "probation", "stunaddr", "turnaddr", "turnusername", "turnpassword");
		foreach ($ssvars as $v) {
			$res = $ss->getConfig($v);
			if ($res) {
				$retvar['rtp_additional.conf']['general'][$v] = strtolower($res);
			}
		}

		return $retvar;
	}

	public function writeConfig($config) {
		$this->FreePBX->WriteConfig($config);
	}


	public function doDialplanHook(&$ext, $null, $null) {
		$ext->addGlobal('ALLOW_SIP_ANON', strtolower($this->getConfig("allowanon")));
	}

	// BMO Hooks.

	public function install() {
	}

	public function uninstall() {
	}

	public function backup() {
	}

	public function restore($backup) {
	}

	function mask2cidr($mask){
		$long = ip2long($mask);
		$base = ip2long('255.255.255.255');
		return 32-log(($long ^ $base)+1,2);
	}

}
