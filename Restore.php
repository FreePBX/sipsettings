<?php
namespace FreePBX\modules\Sipsettings;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
	public function runRestore(){
		$settings = $this->getConfigs();
		$backupinfo = $this->getBackupInfo();
		if ($backupinfo['warmspareenabled'] == 'yes') {
			//lets take a copy of local sipsettings
			$localsettings['kvstore'] = $this->FreePBX->Sipsettings->dumpKVStore();
			$localsettings['database'] = $this->FreePBX->Sipsettings->dumpDbConfigs();
			$this->log(_("warmspare enabled"));
			foreach($settings['kvstore']['noid'] as $key => $val) {
				//bindaddress settings
				if($backupinfo['warmspare_remotebind'] =='yes') {
					if($key == 'bindaddr') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'bindport') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'tlsbindaddr') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'tlsbindport') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'tcpextip-0.0.0.0') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'tcplocalnet-0.0.0.0') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'tcpport-0.0.0.0') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'udpextip-0.0.0.0') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'udplocalnet-0.0.0.0') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'udpport-0.0.0.0') {
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
				}
				//nat settings
				if($backupinfo['warmspare_remotenat'] =='yes') {
					if($key == 'localnets') {
						$this->log(_("Excluding Nat settings 'localnets' "));
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
					if($key == 'externip') {
						$this->log(_("Excluding Nat settings 'externip' "));
						$settings['kvstore']['noid'][$key] = $localsettings['kvstore']['noid'][$key];
					}
				}
			}
			//get the local values
			$localsdbbinds = [];
			foreach($localsettings['database'] as $key => $val) {
				$localsdbbinds[$val['keyword']] = $val['data'];
			}
			foreach($settings['database'] as $key => $val) {
				if($backupinfo['warmspare_remotebind'] =='yes') {
					if($val['keyword'] == 'bindaddr') {
						$val['data'] = $localsdbbinds[$val['keyword']];
						$settings['database'][$key] = $val;
					}
					if($val['keyword'] == 'tlsbindaddr') {
						$val['data'] = $localsdbbinds[$val['keyword']];
						$settings['database'][$key] = $val;
					}
					if($val['keyword'] == 'tlsbindport') {
						$val['data'] = $localsdbbinds[$val['keyword']];
						$settings['database'][$key] = $val;
					}
					if($val['keyword'] == 'bindport') {
						$val['data'] = $localsdbbinds[$val['keyword']];
						$settings['database'][$key] = $val;
					}
				}
			}
		}
		foreach ($settings['kvstore'] as $key => $value) {
				$this->FreePBX->Sipsettings->setMultiConfig($value, $key);
		}
		$this->FreePBX->Sipsettings->loadDbConfigs($settings['database']);
	}

	public function processLegacy($pdo, $data, $tables, $unknownTables){
		$this->restoreLegacyDatabaseKvstore($pdo);
	}
}
