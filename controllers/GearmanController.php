<?php
namespace mkpur\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Console;

/**
 * Runs gearman worker for mkpur
 */
class GearmanController extends \yii\console\Controller
{
  /**
   * Runs gearman worker for mkpur
   */
  public function actionIndex(){
    ini_set('memory_limit','128M');
    echo '[database connection]',PHP_EOL;
    echo '  biddb  : '.$this->module->biddb->dsn,PHP_EOL;
    echo '  infodb : '.$this->module->infodb->dsn,PHP_EOL;
    echo '[gearman woker]',PHP_EOL;
    echo '  server   : '.$this->module->gman_server,PHP_EOL;
    echo '  function : "mkpur_work"',PHP_EOL;
    echo 'start woker...',PHP_EOL;

    $worker=new \GearmanWorker;
    $worker->addServers($this->module->gman_server);
    $worker->addFunction('mkpur_work',[$this,'mkpur_work']);
    while($worker->work());
  }

  public function mkpur_work($job){
    $workload=$job->workload();
    echo $workload,PHP_EOL;
    $workload=Json::decode($workload);
  }
}

