<?php
namespace FreePBX\modules\Sipsettings;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
	public function runRestore(){
		$settings = $this->getConfigs();
		foreach ($settings['kvstore'] as $key => $value) {
				$this->FreePBX->Sipsettings->setMultiConfig($value, $key);
		}
		$this->FreePBX->Sipsettings->loadDbConfigs($settings['database']);
	}

	public function processLegacy($pdo, $data, $tables, $unknownTables){
		$this->restoreLegacyDatabaseKvstore($pdo);
	}
}
