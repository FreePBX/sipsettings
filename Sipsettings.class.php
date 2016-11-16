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
		"tcpport-0.0.0.0" => "5160", // Defaults, only used if this is an upgrade
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
		// Before we hand off to the general POST,
		// if someone has posted from the main page, the
		// 'tlsportowner' will be set. If it is, make
		// sure we do have it set correctly before the
		// page is rendered.
		if (isset($_REQUEST['tlsportowner'])) {
			$this->updateTlsOwner($_REQUEST['tlsportowner']);
		}

		$this->doGeneralPost();

		// Whenever someone visits the sipsettings page, we want to
		// make sure there's no port conflicts.
		//
		// If there are, this will fix it BEFORE the page is displayed,
		// so the user sees the correct, fixed, value. We run it after
		// the post handler above, so it fixes things that users may have
		// entered incorrectly.
		$this->validateNoPortConflicts();
	}

	/**
	 * Return the ports that each channel driver is bound to.
	 *
	 * If $flatten is set to 'true', it assumes everything is listening
	 * on every interface, and returns the array as a single dimension,
	 * with the listen address hard coded to [::]
	 *
	 * If not, returns the binds exactly as configured.
	 *
	 * @param $flatten bool Flatten array
	 * @return array
	 */
	public function getBinds($flatten = false) {
		$binds = array();

		// Note that verifyNoPortConflicts relies on this being constructed
		// with pjsip first, then chansip. Don't change the order.

		$driver = $this->FreePBX->Config->get_conf_setting('ASTSIPDRIVER');
		if ($driver == "both" || $driver == "chan_pjsip") {
			$b = $this->getConfig("binds");
			$b = is_array($b) ? $b : array();
			foreach($b as $protocol => $bind) {
				foreach($bind as $ip => $state) {
					if($state != "on") {
						continue;
					}
					$p = $this->getConfig($protocol."port-".$ip);
					if ($flatten) {
						$binds['pjsip']['[::]'][$protocol] = $p;
					} else {
						$binds['pjsip'][$ip][$protocol] = $p;
					}
				}
			}
		} else {
			$binds['pjsip'] = array("0.0.0.0" => array());
		}

		if ($driver == "both" || $driver == "chan_sip") {
			$out = $this->getChanSipSettings();
			// We assume we are ALWAYS listening on udp, as there's no way to disable it
			// with chansip.
			//
			// Note: chansip is unreliable with ipv6. Leave this default to 0.0.0.0 for
			// the moment.
			$out['bindaddr'] = !empty($out['bindaddr']) ? $out['bindaddr'] : '0.0.0.0';
			$out['bindport'] = !empty($out['bindport']) ? $out['bindport'] : '5060';
			if ($flatten) {
				$out['bindaddr'] = '[::]';
			}
			$binds['sip'][$out['bindaddr']]['udp'] = $out['bindport'];

			// Is 'tcpenabled' set to yes? If so, we're also listening on the bindport
			// in TCP.
			if (isset($out['tcpenable']) && $out['tcpenable'] == "yes") {
				$binds['sip'][$out['bindaddr']]['tcp'] = $out['bindport'];
			}

			// If TLS is enabled, we are also listening on the TLS port.
			if (isset($out['tlsenable']) && $out['tlsenable'] !== "no") {
				if ($flatten) {
					$tlslistenaddr = '[::]';
				} else {
					// TLS is TCP. This should be OK to default to [::] for chansip
					$tlslistenaddr = !empty($out['tlsbindaddr']) ? $out['tlsbindaddr'] : '[::]';
				}
				$tlsport = !empty($out['tlsbindport']) ? $out['tlsbindport'] : '5061';
				$binds['sip'][$tlslistenaddr]['tls'] = $tlsport;
			}
		} else {
			$binds['sip'] = array("0.0.0.0" => array());
		}
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

		if (isset($_REQUEST['pjsipcertid'])) {
			$this->setConfig('pjsipcertid', $_REQUEST['pjsipcertid']);
		}

		if (isset($_REQUEST['method'])) {
			$this->setConfig('method', $_REQUEST['method']);
		}

		if (isset($_REQUEST['verify_client'])) {
			$this->setConfig('verify_client', $_REQUEST['verify_client']);
		}

		if (isset($_REQUEST['verify_server'])) {
			$this->setConfig('verify_server', $_REQUEST['verify_server']);
		}

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


	public function doDialplanHook(&$ext, $null, $null_) {
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
				// Check if this is 'tcpenable' and return this as part of the array,
				// as well as providing it as a custom key. This is used by getBinds.
				//
				// There's no plan to add this to the GUI for chansip, if you want to
				// use UNENCRYPTED TCP, then use pjsip.
				//
				if ($var['keyword'] == "tcpenable") {
					$sip_settings['tcpenable'] = $var['data'];
				}
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
			'context' => 'from-sip-external', 'ALLOW_SIP_ANON' => 'no', 'bindaddr' => '', 'bindport' => '', 'allowguest' => 'yes',
			'srvlookup' => 'no', 'callevents' => 'no', 'sip_custom_key_0' => '', 'sip_custom_val_0' => '', 'tcpenable' => 'no', 'callerid' => 'Unknown');

		return $arr;
	}

	public function updateChanSipSettings($key, $val = false, $type = SELF::SIP_NORMAL, $seq = 10) {
		$db = \FreePBX::Database();
		// Delete the key we want to change
		$del = $db->prepare('DELETE FROM `sipsettings` WHERE `keyword`=? AND `type`=?');
		$del->execute(array($key, $type));

		// If val is not EXACTLY false, add it back in
		if ($val !== false) {
			$ins = $db->prepare('INSERT INTO `sipsettings` (`keyword`, `data`, `type`, `seq`) VALUES (?, ?, ?, ?)');
			$ins->execute(array($key, $val, $type, $seq));
		}
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
					$this->tlsCache['ca_list_file'] = $cert['files']['ca-bundle'];
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


	/**
	 * Determine which SIP Channel driver has port 5061/tcp
	 *
	 * Returns either "sip" (legacy chansip), "pjsip" or "none"
	 *
	 * @return string
	 */
	public function getTlsPortOwner() {
		$owner = "none";

		// Get our binds
		$binds = $this->getBinds(true);

		// Start by checking if pjsip owns it
		if (isset($binds['pjsip']) && $binds['pjsip']) {
			foreach ($binds['pjsip'] as $listen => $proto) {
				foreach ($proto as $p => $port) {
					if ((int) $port === 5061) {
						// If this is NOT 'udp', it's tcp.
						if ($p !== "udp") {
							$owner = "pjsip";
							break;
						}
					}
				}
			}
		}

		// Let's see if chansip knows about it.
		if (isset($binds['sip']) && $binds['sip']) {
			foreach ($binds['sip'] as $listen => $proto) {
				foreach ($proto as $p => $port) {
					if ((int) $port === 5061) {
						// If this is NOT 'udp', it's tcp.
						if ($p !== "udp") {
							// chansip is trying to use this port. Is pjsip trying
							// to use it too?
							if ($owner !== "none") {
								// Well poot. Change chansip to be the 'other' TLS port,
								// which is 5161/TCP
								$this->updateChanSipSettings("tlsbindport", 5161);
								// TODO: Notify here?
							} else {
								$owner = "sip";
								break;
							}
						}
					}
				}
			}
		}
		return $owner;
	}

	/**
	 * Determine which SIP Channel driver is listening for SIP packets on port 5060/udp
	 *
	 * Returns either "sip" (legacy chansip), "pjsip" or "none"
	 *
	 * @return string
	 */
	public function getSipPortOwner() {
		// Get our binds
		$binds = $this->getBinds(true);

		// Start by checking if pjsip owns it
		if (isset($binds['pjsip']) && $binds['pjsip']) {
			foreach ($binds['pjsip'] as $listen => $proto) {
				foreach ($proto as $p => $port) {
					if ($p !== "udp") {
						continue;
					} else {
						if ((int) $port === 5060) {
							return "pjsip";
						}
					}
				}
			}
		}

		// Not pjsip. How about chansip?
		if (isset($binds['sip']) && $binds['sip']) {
			foreach ($binds['sip'] as $listen => $proto) {
				foreach ($proto as $p => $port) {
					if ($p !== "udp") {
						continue;
					} else {
						if ((int) $port === 5060) {
							return "sip";
						}
					}
				}
			}
		}

		// Neither of them.
		return "none";
	}

	/**
	 * Return the bound port for the protocol specified.
	 *
	 * Will return (bool) false if protocol is not used by that driver
	 *
	 * @param string $driver One of 'sip', 'chansip', or 'pjsip'
	 * @param string $proto One of 'udp', 'tcp', or 'tls'
	 *
	 * @return int|bool
	 */
	public function getDriverPort($driver = false, $proto = false) {
		if (!$driver || !$proto) {
			throw new \Exception("No driver or port requested");
		}
		$binds = $this->getBinds(true);
		$sanedriver = strtolower($driver);
		if ($sanedriver == "sip" || $sanedriver == "chansip") {
			$check = $binds['sip']['[::]'];
		} elseif ($sanedriver == "pjsip") {
			$check = $binds['pjsip']['[::]'];
		} else {
			throw new \Exception("Unknown sip driver '$driver'");
		}

		if (!isset($check[$proto]) || !$check[$proto]) {
			return false;
		} else {
			return (int) $check[$proto];
		}
	}

	/**
	 * Make sure that none of the SIP channel drivers have conflicting ports
	 *
	 * This will give priority to PJSIP owning the ports.
	 */
	public function validateNoPortConflicts() {

		// Get all of our binds
		$binds = $this->getBinds(true);

		$allports = array("tcp" => array(), "udp" => array());
		foreach ($binds as $driver => $listenarr) {
			// We explicitly don't care about interfaces. Having
			// chansip on 5060 on int1 and pjsip on 5060 on int2
			// is just going to be a nightmare. We asked getBinds
			// to return a flattened array, so we just disregard
			// the interface
			foreach ($listenarr as $ports) {
				foreach ($ports as $proto => $port) {
					// Phew. Finally.
					// Is it a websocket port? We don't care about them
					if ($proto == "wss" || $proto == "ws") {
						continue;
					}
					// Is it a TCP port?
					if ($proto !== "udp") {
						$type = "tcp";
					} else {
						$type = "udp";
					}
					// Is there a conflict?
					if (isset($allports[$type][$port])) {
						// Yes. Poot.
						$n = \FreePBX::Notifications();

						// If this isn't chansip, then somehow the user has managed
						// to do something crazy to pjsip.  We can't fix it.
						if ($driver !== "sip") {
							$n->add_critical("sipsettings", "unknownpjsip", _("Unknown Port Conflict"), _("An unknown port conflict has been detected in PJSIP. Please check and validate your PJSIP Ports to ensure they're not overlapping"), "", true, true);
							continue;
						}

						// So, is this udp, tcp, or tls?
						if ($proto == "udp") {
							// Try a couple of ports until we find a spare. Default is first,
							// just in case we have a conflict on 5061 or something.
							$attempts = array(5060, 5062, 5161, 5199, 5260, 15060);
							foreach ($attempts as $portattempt) {
								if (!isset($allports['udp'][$portattempt])) {
									// Yes. Found a spare.
									$this->updateChanSipSettings("bindport", $portattempt);
									$allports['udp'][$portattempt] = true;
									$n->add_critical("sipsettings", "sipmoved", _("CHANSIP Port Moved"), sprintf(_("Chansip was assigned the same port as pjsip for UDP traffic. The Chansip port has been changed to %s"), $portattempt), "", true, true);
									needreload();
									break;
								}
							}
						} elseif ($proto == "tcp") {
							// This means pjsip is listening on TCP, and, someone's turned on tcpenable
							// in chansip settings.  We just turn it off, as chansip can't move its tcp
							// port.
							$this->updateChanSipSettings("tcpenable");
							$n->add_critical("sipsettings", "siptcpdisabled", _("CHANSIP TCP Disabled"), _("Chansip was assigned the same port as pjsip for TCP traffic. Chansip has had the tcpenable setting removed, and is no longer listening for TCP connections."), true, true);
							needreload();
							continue;
						} elseif ($proto == "tls") {
							// TLS is conflicting with PJSIP. Try to find a spare port
							$attempts = array(5061, 5161, 5162, 5199, 5261, 15061);
							foreach ($attempts as $portattempt) {
								if (!isset($allports['tcp'][$portattempt])) {
									// Yes. Found a spare.
									$this->updateChanSipSettings("tlsbindport", $portattempt);
									$allports['tcp'][$portattempt] = true;
									$n->add_critical("sipsettings", "siptlsmoved", _("CHANSIP TLS Port Moved"), sprintf(_("Chansip was assigned a port that was already in use for TLS traffic. The Chansip TLS port has been changed to %s"), $portattempt), true, true);
									needreload();
									break;
								}
							}
						} else {
							throw new \Exception("Unknown protocol ($proto) to fix");
						}
					} // No conflict!
					$allports[$type][$port] = true;
					// Debugging help
					// $allports[$type][$port] = "$driver, $proto on port $port ($type)";
				}
			}
		}
	}

	/**
	 * Update the TLS Port if requested
	 *
	 * This is called when 'tlsportowner' is submitted as part of the POST.
	 * It checks to see if the requested channel driver does own the TLS port,
	 * and if it doesn't, it assigns it.
	 *
	 * If the other driver is assigned to that port, it moves it to 5161.
	 */
	public function updateTlsOwner($driver = false) {
		// Who owns it at the moment?
		$owner = $this->getTlsPortOwner();
		if ($owner == $driver) {
			// Nothing to do, we're already correct
			return true;
		}

		// Does chan_sip want to own the TLS port?
		if ($driver == "sip") {
			// We're moving chansip to 5061
			$this->updateChanSipSettings("tlsbindport", 5061);
			needreload();

			// If pjsip is listening on 5061, move it to 5161
			$pjsipbinds = $this->getConfig("binds");
			$pjsipbinds = is_array($pjsipbinds) ? $pjsipbinds : array();

			foreach($pjsipbinds as $pjproto => $binds) {
				// Skip if not tls
				if ($pjproto !== "tls") {
					continue;
				}

				// Get all the listening TLS interfaces and if they're
				// set to 5061, set them to 5161
				foreach($binds as $ip => $state) {
					$p = $this->getConfig($pjproto."port-".$ip);
					if ($p == 5061) {
						// It's a conflict. move to 5161
						$this->setConfig($pjproto."port-".$ip, 5161);
					}
				}
			}
		} elseif ($driver = "pjsip") {
			// We're setting pjsip to own 5061. Does chansip think it
			// owns it?
			$chansip = $this->getChanSipSettings();
			if (!isset($chansip['tlsbindport']) || !$chansip['tlsbindport'] || $chansip['tlsbindport'] == 5061) {
				// Yes it does. Move to 5161.
				$this->updateChanSipSettings("tlsbindport", 5161);
				needreload();
			}

			// Update all tls listeners for pjsip to listen on 5061
			$pjsipbinds = $this->getConfig("binds");
			$pjsipbinds = is_array($pjsipbinds) ? $pjsipbinds : array();
			foreach($pjsipbinds as $pjproto => $binds) {
				// Skip if not tls
				if ($pjproto !== "tls") {
					continue;
				}

				// Get all the listening TLS interfaces and if they're
				// set to 5061, set them to 5161
				foreach($binds as $ip => $state) {
					$p = $this->getConfig($pjproto."port-".$ip);
					if ($p != 5061) {
						// It's a conflict. move to 5161
						$this->setConfig($pjproto."port-".$ip, 5061);
						needreload();
					}
				}
			}
		} else {
			throw new \Exception("Can't change tls owner to unknown driver '$driver'");
		}
	}
}
