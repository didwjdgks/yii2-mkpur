<?php
namespace mkpur\models\infodb;

use mkpur\Module;

class V3BidKey extends \yii\db\ActiveRecord
{
  public static function tableName(){
    return 'v3_bid_key';
  }

  public static function getDb(){
    return Module::getInstance()->infodb;
  }

  public function beforeSave($insert){
    return false;
  }

  public function beforeDelete(){
    return false;
  }

  public function afterFind(){
    parent::afterFind();

    if($this->constnm) $this->constnm=iconv('cp949','utf-8',$this->constnm);
    if($this->org) $this->org=iconv('cp949','utf-8',$this->org);
    if($this->notinum) $this->notinum=iconv('cp949','utf-8',$this->notinum);
  }
}

