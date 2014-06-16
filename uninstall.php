<?php
/* $Id:$ */
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

sql("DELETE FROM kvstore WHERE module = 'sipsettings'");
sql("DROP TABLE `sipsettings`");
