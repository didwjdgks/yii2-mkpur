<?php
namespace mkpur\models\infodb;

use mkpur\Module;

class V3BidValue extends \yii\db\ActiveRecord
{
  public static function tableName(){
    return 'v3_bid_value';
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
    if($this->realorg) $this->realorg=iconv('cp949','utf-8',$this->realorg);
  }
}

