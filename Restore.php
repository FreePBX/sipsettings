<?php
namespace FreePBX\modules\Sipsettings;
use FreePBX\modules\Backup as Base;
class Restore Extends Base\RestoreBase{
  public function runRestore($jobid){
    $settings = $this->getConfigs();
    foreach ($settings['kvstore'] as $key => $value) {
        $this->FreePBX->Sipsettings->setMultiConfig($value, $key);
    }
    $this->FreePBX->Sipsettings->loadDbConfig($settings['database']);
  }
}