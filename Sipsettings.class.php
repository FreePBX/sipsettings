<?php
// vim: set ai ts=4 sw=4 ft=php:

class Sipsettings extends DB_Helper {

	private $pagename = null;

	public static $dbDefaults = array( "rtpstart" => "10000", "rtpend" => "20000" );

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
			return $this->getRTPSettings();
		} elseif ($this->pagename == "chansip") {
			return "Chan_Sip stuff here\n";
		} elseif ($this->pagename == "pjsip") {
			return "PJSip stuff here\n";
		} else {
			return "I DON'T KNOW\n";
		}
	}

	public function getRTPSettings() {
		// This is messy. Copied and pasted. Sorry.
		$str = "<h4>"._("RTP Settings")."</h4><p><a href='#' class='info'>"._("RTP Port Ranges")."<span>"._("The starting and ending RTP port range")."</span></a>";
		$str .= "<input type='text' size='5' id='rtpstart' name='rtpstart' class='validate-int' value='".$this->getConfig('rtpstart')."' tabindex=".++$tabindex.">";
		$str .= "<small>&nbsp;(rtpstart)</small>&nbsp;<input type='text' size='5' id='rtpend' name='rtpend' class='validate-int' value='".$this->getConfig('rtpend');
		$str .=	"' tabindex=".++$tabindex."><small>&nbsp;(rtpend)</small>&nbsp;</p>\n";
		$str .= "<p>RTP Checksums</p> <p>Strict RTP</p> <p>ICE Support</p> <p>STUN Server</p> <p>TURN server + User + Password</p>\n";

		return $str;
	}

}
