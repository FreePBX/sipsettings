<?php
// vim: set ai ts=4 sw=4 ft=php:

class Sipsettings extends FreePBX_Helpers implements BMO {

	const SIP_NORMAL = 0;
	const SIP_CODEC = 1;
	const SIP_VIDEO_CODEC = 2;
	const SIP_CUSTOM = 9;

	private $pagename = null;
	private $pagedata = null;
	private $tlsCache = null;

	public static $dbDefaults = array(
		"rtpstart" => "10000", "rtpend" => "20000",
		"stunaddr" => "",
		"turnaddr" => "",
		"turnusername" => "",
		"turnpassword" => "",
		"protocols" => array("udp", "tcp", "tls", "ws", "wss"),
		"rtpchecksums" => "Yes",
		"strictrtp" => "Yes",
		"allowguest" => "no",
		"allowanon" => "No",
		"showadvanced" => "no",
		"tcpport-0.0.0.0" => "5061", // Defaults, only used if this is an upgrade
		"udpport-0.0.0.0" => "5061",
		"tlsport-0.0.0.0" => "5161",
	);

	public function ajaxRequest($req, &$setting) {
		// We're happy to do Ajax
		return true;
	}

	public function ajaxHandler() {
		if ($_REQUEST['command'] == "getnetworking") {
			if (!class_exists('FreePBX\Modules\Sipsettings\NatGet')) {
				include __DIR__."/Natget.class.php";
			}
			try {
				$nat = new \FreePBX\Modules\Sipsettings\NatGet();
				$ip = $nat->getVisibleIP();
				if($ip['status']) {
					$retarr = array("status" => true, "externip" => $ip['address'], "routes" => $nat->getRoutes());
				} else {
					$retarr = array("status" => true, "externip" => false, "routes" => $nat->getRoutes(), "externipmesg" => $ip['message']);
				}
			} catch(\Exception $e) {
				$retarr = array("status" => false, "message" => $e->getMessage());
			}
			return $retarr;
		}
		return false;
	}

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
				$this->pagename = "general";
				$this->doGeneralPost();
			} else {
				// Unknown pagename?
				// thow new Exception("WTF. You suck");
			}
		}
	}

	public function getBinds() {
		$binds = array();
		$b = $this->getConfig("binds");
		$b = is_array($b) ? $b : array();
		foreach($b as $protocol => $bind) {
			foreach($bind as $ip => $state) {
				if($state != "on") {
					continue;
				}
				$p = $this->getConfig($protocol."port-".$ip);
				$binds['pjsip'][$ip][$protocol] = $p;
			}
		}
		if(!function_exists('sipsettings_get')) {
			//urgh......
			include __DIR__."/functions.inc.php";
		}
		$out = sipsettings_get();
		$out['bindaddr'] = !empty($out['bindaddr']) ? $out['bindaddr'] : '0.0.0.0';
		$out['bindport'] = !empty($out['bindport']) ? $out['bindport'] : '5060';
		$binds['sip'][$out['bindaddr']]['udp'] = $out['bindport'];
		return $binds;
	}

	public function getActiveModules() {

		$driver = $this->FreePBX->Config->get_conf_setting('ASTSIPDRIVER');

		$str = _("Asterisk is currently using %s for SIP Traffic.");
		if ($driver == "both") {
			$str = sprintf($str,_("chan_pjsip and chan_sip"));
		} else {
			$str = sprintf($str,$driver);
		}
		$str .= "<br />"._("You can change this on the Advanced Settings Page");

		return $str;
	}

	public function myShowPage() {
		if(empty($this->pagedata)) {
			$driver = $this->FreePBX->Config->get_conf_setting('ASTSIPDRIVER');

			$this->pagedata = array(
				"general" => array(
					"name" => _("General SIP Settings"),
					"page" => 'general.page.php'
				)
			);

			if ($driver == "chan_sip" || $driver == "both") {
				$this->pagedata['sip'] = array(
					"name" => _("Chan SIP Settings"),
					"page" => 'chansip.page.php'
				);
			}

			if ($driver == "chan_pjsip" || $driver == "both") {
				$this->pagedata['pjsip'] = array(
					"name" => _("Chan PJSIP Settings"),
					"page" => 'chanpj.page.php'
				);
			}

			foreach($this->pagedata as &$page) {
				ob_start();
				include($page['page']);
				$page['content'] = ob_get_contents();
				ob_end_clean();
			}
		}

		return $this->pagedata;
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
				$this->setCodecs('audio',$newcodecs);
			} else {
				// They turned off ALL the codecs. Set them back to default.
				$this->setCodecs('audio');
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
			// Renumber the array
			if (!empty($_REQUEST['localnets'])) {
				$_REQUEST['localnets'] = array_values($_REQUEST['localnets']);
			}
		}

		// Grab the global externalip, if it's there
		if (isset($_REQUEST['externip'])) {
			$this->setConfig('externip', $_REQUEST['externip']);
		}
/*
		if (isset($_REQUEST['allowguest'])) {
			$this->setConfig('allowguest', $_REQUEST['allowguest']);
		}
*/
		// This is in Request_Helper.class.php
		$ignored = $this->importRequest(null, "/(.+)bindip-(.+)$/");
		// There may be binds that matched..
		$binds = array();
		foreach ($ignored as $key => $var) {
			if (preg_match("/(.+)bindip-(.+)$/", $key, $match)) {
				$ip = str_replace("_", ".", $match[2]);
				$binds[$match[1]][$ip] = $var;
				continue;  // Don't save them
			}
		}

		if (!empty($binds)) {
			$this->setConfig("binds", $binds);
		}

		needreload();
	}

	private function radioset($id, $name, $help = "", $values, $current) {
		$out =  "<tr><td><a class='info'>$name<span>$help</span></a></td>\n";
		$out .= "<td><span class='radioset'>\n";
		foreach ($values as $k => $v) {
			$out .= "<input id='$id-$k' name='$id' value='$k' type='radio'";
			if ($current === $k) {
				$out .= " checked";
			}
			$out .= "><label for='$id-$k'>$v</label>\n";
		}
		$out .= "</span></td></tr>\n";

		return $out;
	}

	public function genConfig() {

		// RTP Configuration
		$ssvars = array("rtpstart", "rtpend", "rtpchecksums", "strictrtp", "dtmftimeout", "probation", "stunaddr", "turnaddr", "turnusername", "turnpassword");
		foreach ($ssvars as $v) {
			$res = $this->getConfig($v);
			if ($res && trim($res) != "") {
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

	/**
	 * Retrieve Active Codecs
	 * @param {string} $type               The Codec Type
	 * @param {bool} $showDefaults=false Whether to show defaults or not
	 */
	public function getCodecs($type,$showDefaults=false) {
		switch($type) {
			case 'audio':
				$codecs = $this->getConfig('voicecodecs');
			break;
			case 'video':
				$codecs = $this->getConfig('videocodecs');
			break;
			case 'text':
				$codecs = $this->getConfig('textcodecs');
			break;
			case 'image':
				$codecs = $this->getConfig('imagecodecs');
			break;
			default:
				throw new Exception(_('Unknown Type'));
			break;
		}

		if(empty($codecs) || !is_array($codecs)) {
			switch($type) {
				case 'audio':
					$codecs = $this->FreePBX->Codecs->getAudio(true);
				break;
				case 'video':
					$codecs = $this->FreePBX->Codecs->getVideo(true);
				break;
				case 'text':
					$codecs = $this->FreePBX->Codecs->getText(true);
				break;
				case 'image':
					$codecs = $this->FreePBX->Codecs->getImage(true);
				break;
			}
		}

		if($showDefaults) {
			switch($type) {
				case 'audio':
					$allCodecs = $this->FreePBX->Codecs->getAudio();
				break;
				case 'video':
					$allCodecs = $this->FreePBX->Codecs->getVideo();
				break;
				case 'text':
					$allCodecs = $this->FreePBX->Codecs->getText();
				break;
				case 'image':
					$allCodecs = $this->FreePBX->Codecs->getImage();
				break;
			}
			// Update the $codecs array by adding un-selected codecs to the end of it.
			foreach ($allCodecs as $c => $v) {
				if (!isset($codecs[$c])) {
					$codecs[$c] = false;
				}
			}
			return $codecs;
		} else {
			//Remove all non digits
			$final = array();
			foreach($codecs as $codec => $order) {
				$order = trim($order);
				if(ctype_digit($order)) {
					$final[$codec] = $order;
				}
			}
			asort($final);
			return $final;
		}
	}

	/**
	 * Update or Set Codecs
	 * @param {string} $type           Codec Type
	 * @param {array} $codecs=array() The codecs with order, if blank set defaults
	 */
	public function setCodecs($type,$codecs=array()) {
		$default = empty($codecs) ? true : false;
		switch($type) {
			case 'audio':
				$codecs = $default ? $this->FreePBX->Codecs->getAudio(true) : $codecs;
				$this->setConfig("voicecodecs", $codecs);
			break;
			case 'video':
				$codecs = $default ? $this->FreePBX->Codecs->getVideo(true) : $codecs;
				$this->setConfig("videocodecs", $codecs);
			break;
			case 'text':
				$codecs = $default ? $this->FreePBX->Codecs->getText(true) : $codecs;
				$this->setConfig("textcodecs", $codecs);
			break;
			case 'image':
				$codecs = $default ? $this->FreePBX->Codecs->getImage(true) : $codecs;
				$this->setConfig("imagecodecs", $codecs);
			break;
			default:
				throw new Exception(_('Unknown Type'));
			break;
		}
		return true;
	}

	public function getChanSipSettings($returnraw = false) {
		$sql = "SELECT `keyword`, `data`, `type`, `seq` FROM `sipsettings` WHERE type != 1 AND type != 2 ORDER BY `type`, `seq`";
		$raw_settings = sql($sql,"getAll",DB_FETCHMODE_ASSOC);

		if ($returnraw === true) {
			return $raw_settings;
		}

		$sip_settings = $this->getChanSipDefaults();

		foreach ($raw_settings as $var) {
			switch ($var['type']) {
			case self::SIP_NORMAL:
				$sip_settings[$var['keyword']]                 = $var['data'];
				break;
			case self::SIP_CUSTOM:
				$sip_settings['sip_custom_key_'.$var['seq']]   = $var['keyword'];
				$sip_settings['sip_custom_val_'.$var['seq']]   = $var['data'];
				break;
			default:
				throw new \Exception("Unknown type in sipsettings - ".$var['type']);
			}
		}

		return $sip_settings;
	}

	public function getChanSipDefaults() {
		$arr = array ( 'nat' => 'yes', 'nat_mode' => 'externip', 'externrefresh' => '120', 'g726nonstandard' => 'no',
			't38pt_udptl' => 'no', 'videosupport' => 'no', 'maxcallbitrate' => '384', 'canreinvite' => 'no', 'rtptimeout' => '30',
			'rtpholdtimeout' => '300', 'rtpkeepalive' => '0', 'checkmwi' => '10', 'notifyringing' => 'yes', 'notifyhold' => 'yes',
			'registertimeout' => '20', 'registerattempts' => '0', 'maxexpiry' => '3600', 'minexpiry' => '60', 'defaultexpiry' => '120',
			'jbenable' => 'no', 'jbforce' => 'no', 'jbimpl' => 'fixed', 'jbmaxsize' => '200', 'jbresyncthreshold' => '1000', 'jblog' => 'no',
			'context' => '', 'ALLOW_SIP_ANON' => 'no', 'bindaddr' => '', 'bindport' => '', 'allowguest' => 'yes',
			'srvlookup' => 'no', 'callevents' => 'no', 'sip_custom_key_0' => '', 'sip_custom_val_0' => '');

		return $arr;
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
	public function getActionBar($request) {
		$buttons = array();
		switch($request['display']) {
			case 'sipsettings':
				$buttons = array(
					'reset' => array(
						'name' => 'reset',
						'id' => 'reset',
						'value' => _('Reset')
					),
					'submit' => array(
						'name' => 'submit',
						'id' => 'submit',
						'value' => _('Submit')
					)
				);
			break;
		}
		return $buttons;
	}

	/**
	 * Generate TLS configuration
	 *
	 * This returns a k=>v array of entries to add to a transport if
	 * TLS is enabled.
	 *
	 * If TLS fails validation, an empty array is returned.
	 *
	 * @return array as above
	 */
	public function getTLSConfig() {

		// Cache is here as this function is called for every extension that has
		// the ability to do srtp.

		if (is_array($this->tlsCache)) {
			return $this->tlsCache;
		}

		if($this->FreePBX->Modules->moduleHasMethod("certman","getDefaultCertDetails")) {
			$cerid = $this->getConfig('pjsipcertid');
			$cert = $this->FreePBX->Certman->getCertificateDetails($cerid);
			if(!empty($cert['files']['crt']) && !empty($cert['files']['key'])) {
				$this->tlsCache = array(
					"cert_file" => $cert['files']['crt'],
					"priv_key_file" => $cert['files']['key'],
				);
				if(isset($cert['files']['ca-bundle'])) {
					$this->tlsCache['ca-bundle'] = $cert['files']['ca-bundle'];
				}
			} else {
				$this->tlsCache = array();
				return $this->tlsCache;
			}
		} else {
			$defaults = array(
				"ca_list_file" => "/etc/asterisk/keys/integration/ca-bundle.crt",
				"cert_file" => "/etc/asterisk/keys/integration/webserver.crt",
				"priv_key_file" => "/etc/asterisk/keys/integration/webserver.key",
			);

			$map = array(
				"calistfile" => "ca_list_file",
				"certfile" => "cert_file",
				"privkeyfile" => "priv_key_file",
			);

			$retarr = array();

			foreach ($map as $k => $v) {
				$tmp = $this->getConfig($k);
				if ($tmp) {
					// It's set. Does it exist?
					if (file_exists($tmp)) {
						// That'll do.
						$retarr[$v] = $tmp;
					} else {
						// Pointed to a file that doesn't exist? No TLS.
						// TODO: Notification?
						$cache = array();
						return array();
					}
				} else {
					// Notset. Does the default file exist?
					if (file_exists($defaults[$v])) {
						$retarr[$v] = $defaults[$v];
					} else {
						// No default file.
						$cache = array();
						return array();
					}
				}
			}
			$this->tlsCache = $retarr;
		}
		if(!empty($this->tlsCache)) {
			$check = array('method','verify_client','verify_server');
			foreach($check as $i) {
				$v = $this->getConfig($i);
				if(!empty($v)) {
					$this->tlsCache[$i] = $v;
				}
			}
		}

		return $this->tlsCache;
	}
}
