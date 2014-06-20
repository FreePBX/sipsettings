<?php

include '/etc/freepbx.conf';

global $version;

print "$version\n";
	$version = FreePBX::Config()->get('ASTVERSION');
print "$version\n";


