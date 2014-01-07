<?php
// vim: set ai ts=4 sw=4 ft=php:

// SipSettings page. Re-written for usage with chan_sip and chan_pjsip
// AGPL v3 Licened

// Note that BEFORE THIS IS CALLED, the Sipsettings configPageinit
// function is called. This is where you do any changes. The page.foo.php
// is only for DISPLAYING things.  MVC is a cool idea, ya know?
//

$ss = FreePBX::create()->Sipsettings;

print $ss->getRnav();

print " <h2>"._("SIP Settings")."</h2>\n";

print $ss->getActiveModules();

print $ss->showPage();

?>


