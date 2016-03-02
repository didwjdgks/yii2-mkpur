<?php
namespace mkpur\models\biddb;

use mkpur\Module;

class BidpurSearch extends \yii\db\ActiveRecord
{
  public static function tableName(){
    return 'bidpur_search';
  }

  public static function getDb(){
    return Module::getInstance()->biddb;
  }
}

