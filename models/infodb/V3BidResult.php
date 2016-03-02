<?php
namespace mkpur\models\infodb;

use mkpur\Module;

class V3BidResult extends \yii\db\ActiveRecord
{
  public static function tableName(){
    return 'v3_bid_result';
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
    if($this->officenm1) $this->officenm1=iconv('cp949','utf-8',$this->officenm1);
    if($this->prenm1) $this->prenm1=iconv('cp949','utf-8',$this->prenm1);
  }
}

