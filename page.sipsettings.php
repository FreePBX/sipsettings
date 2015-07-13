<?php
// vim: set ai ts=4 sw=4 ft=php:

// SipSettings page. Re-written for usage with chan_sip and chan_pjsip
// AGPL v3 Licened

// Note that BEFORE THIS IS CALLED, the Sipsettings configPageinit
// function is called. This is where you do any changes. The page.foo.php
// is only for DISPLAYING things.  MVC is a cool idea, ya know?
//
$ss = FreePBX::create()->Sipsettings;
?>

<div class="container-fluid">
	<h1><?php echo _("SIP Settings")?></h1>
		<?php echo \show_help($ss->getActiveModules(),_("SIP driver information")); ?>
	<div class = "display full-border">
		<div class="row">
			<div class="col-sm-9">
				<div class="fpbx-container">
					<div class="display full-border">
						<?php echo $ss->myShowPage();  ?>
					</div>
				</div>
			</div>
			<div class="col-sm-3 bootnav">
				<div class="list-group" id="ssmenu">
					<?php echo $ss->getRnav(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
