<?php
namespace FreePBX\modules\Sipsettings;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
  public function runRestore($jobid){
    $settings = $this->getConfigs();
    foreach ($settings['kvstore'] as $key => $value) {
        $this->FreePBX->Sipsettings->setMultiConfig($value, $key);
    }
    $this->FreePBX->Sipsettings->loadDbConfigs($settings['database']);
  }

  public function processLegacy($pdo, $data, $tables, $unknownTables, $tmpfiledir){
    $tables = array_flip($tables+$unknownTables);
    if(!isset($tables['sipsettings'])){
      return $this;
    }
    $bmo = $this->FreePBX->Sipsettings;
    $bmo->setDatabase($pdo);
    $configs = reset($this->FreePBX->Sipsettings->dumpDbConfigs());
    $bmo->resetDatabase();
    $bmo->loadDbConfigs($settings['database']);
    $this->transformLegacyKV($pdo, 'sipsettings', $this->FreePBX)
      ->transformNamespacedKV($pdo, 'sipsettings', $this->FreePBX);
    return $this;
  }
}
