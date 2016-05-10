<?php



include '/etc/freepbx.conf';

$ss = \FreePBX::Sipsettings();

print_r($ss->getBinds(true));
