<?php
namespace FreePBX\modules\Sipsettings;
use FreePBX\modules\Backup as Base;
class Backup Extends Base\BackupBase{
  public function runBackup($id,$transaction){
    /** For stuff in the kvstore */
    $kvstoreids = $this->FreePBX->Sipsettings->getAllids();
    $kvstoreids[] = 'noid';
    $settings['kvstore'] = [];
    foreach ($kvstoreids as $value) {
        $settings['kvstore'][$value] = $this->FreePBX->Sipsettings->getAll($value);
    }
    /** Database stuff */
    $settings['database'] = $this->FreePBX->Sipsettings->dumpDbConfigs();
    $this->addDependency('core');
    $this->addConfigs($settings);
  }
}