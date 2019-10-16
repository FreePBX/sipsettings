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
						$this->log(_("Excluding Bind address"));
						$settings['database']['bindaddr'] = $localsettings['database']['bindaddr'];
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
