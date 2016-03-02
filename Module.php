<?php
namespace mkpur;

use yii\db\Connection;
use yii\di\Instance;

class Module extends \yii\base\Module
{
  public $biddb='biddb';
  public $infodb='infodb';

  public $gman_server;

  public function init(){
    parent::init();

    $this->biddb=Instance::ensure($this->biddb,Connection::className());
    $this->infodb=Instance::ensure($this->infodb,Connection::className());
  }
}

