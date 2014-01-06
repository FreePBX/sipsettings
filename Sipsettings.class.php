<?php
// vim: set ai ts=4 sw=4 ft=php:

class Sipsettings extends DB_Helper {

	private $pagename = null;

	public static $dbDefaults = array( "rtpstart" => "10000", "rtpend" => "20000", "protocols" => array("udp", "tcp", "ws") );

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
				$this->doPJSipPost();
			} else {
				// Unknown pagename?
				// thow new Exception("WTF. You suck");
			}
		}
	}

	public function getRnav() { 
		$str =  "<div class='rnav'><ul>";

		$pages = array( "general" => "General", "chansip" => "Chan_SIP", "pjsip" => "Chan_PJSip");

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

	public function showPage() {
		if (!$this->pagename) {
			include 'general.page.php';
		} elseif ($this->pagename == "chansip") {
			include 'chansip.page.php';
		} elseif ($this->pagename == "pjsip") {
			$this->getConfig('foop');
			include 'chanpj.page.php';
		} else {
			return "I DON'T KNOW\n";
		}
	}

	// PJSIp POST
	public function doPJSipPost() {
		// Nothing's been submitted, continue along, nothing to see here.
		if (!isset($_REQUEST['Submit']))
			return;

		//print_r($_REQUEST);

		// As we nuke the binds, we want to make sure we DON'T nuke them
		// if no binds were given to us (eg, different sub-page)
		$binds = false;
		foreach ($_REQUEST as $key => $var) {
			// Check for bindip-* posts
			if (preg_match("/(.+)bindip-(.+)$/", $key, $match)) {
				$ip = str_replace("_", ".", $match[2]);
				$binds[$match[1]][$ip] = "on";
				continue;  // Don't save them
			}
			// Now, just save everything else we've been given, excluding a couple of unneeded things
			if ($key == "display" || $key == "type" || $key == "category" || $key == "Submit")
				continue;

			$key = str_replace("_", ".", $key);
			$this->setConfig($key, $var);
		}
		if ($binds) $this->setConfig("binds", $binds);
	}
}
