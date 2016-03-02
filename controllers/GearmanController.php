<?php
namespace mkpur\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Console;

use mkpur\models\infodb\V3BidKey;
use mkpur\models\infodb\V3BidValue;
use mkpur\models\infodb\V3BidResult;

use mkpur\models\biddb\BidpurSearch;

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
    echo '[gearman worker]',PHP_EOL;
    echo '  server   : '.$this->module->gman_server,PHP_EOL;
    echo '  function : "mkpur_work"',PHP_EOL;
    echo 'start worker...',PHP_EOL;

    $worker=new \GearmanWorker;
    $worker->addServers($this->module->gman_server);
    $worker->addFunction('mkpur_work',[$this,'mkpur_work']);
    while($worker->work());
  }

  public function mkpur_work($job){
    $workload=Json::decode($job->workload());

    $this->module->infodb->close();
    $this->module->biddb->close();

    $v3BidKey=V3BidKey::findOne($workload['bidid']);
    if($v3BidKey===null) return;
    if($v3BidKey->bidtype!=='pur') return;

    echo $job->workload(),PHP_EOL;
    echo $v3BidKey->constnm.' ['.$v3BidKey->notinum.'] '.$v3BidKey->org,PHP_EOL;

    $bidpurSearch=BidpurSearch::findOne($v3BidKey->bidid);
    if($bidpurSearch===null){
      $bidpurSearch=new BidpurSearch([
        'bidid'=>$v3BidKey->bidid,
      ]);
    }
    $bidpurSearch->whereis=$v3BidKey->whereis;
    $bidpurSearch->bidtype=$v3BidKey->bidtype;
    $bidpurSearch->notinum=$v3BidKey->notinum;
    $bidpurSearch->orgcode=$v3BidKey->orgcode;
    $bidpurSearch->org=$v3BidKey->org;
    $bidpurSearch->bidproc=$v3BidKey->bidproc;
    $bidpurSearch->contract=$v3BidKey->contract;
    $bidpurSearch->bidcls=$v3BidKey->bidcls;
    $bidpurSearch->succls=$v3BidKey->succls;
    $bidpurSearch->ulevel=$v3BidKey->ulevel;
    $bidpurSearch->itemcode=$v3BidKey->purcode;
    $bidpurSearch->location=$v3BidKey->location;
    $bidpurSearch->convension=$v3BidKey->convention;
    $bidpurSearch->presum=$v3BidKey->presum;
    $bidpurSearch->basic=$v3BidKey->basic;
    $bidpurSearch->pct=$v3BidKey->pct;
    $bidpurSearch->state=$v3BidKey->state;

    $v3BidValue=V3BidValue::findOne($v3BidKey->bidid);
    if($v3BidValue!==null){
      $bidpurSearch->realorg=$v3BidValue->realorg;
      $bidpurSearch->registdt=$v3BidValue->registdt;
      $bidpurSearch->explaindt=$v3BidValue->explaindt;
      $bidpurSearch->agreedt=$v3BidValue->agreedt;
      $bidpurSearch->opendt=$v3BidValue->opendt;
      $bidpurSearch->closedt=$v3BidValue->closedt;
      $bidpurSearch->constdt=$v3BidValue->constdt;
      $bidpurSearch->writedt=$v3BidValue->writedt;
    }

    $v3BidResult=V3BidResult::findOne($v3BidKey->bidid);
    if($v3BidResult!==null){
      $bidpurSearch->reswdt=$v3BidResult->reswdt;
      $bidpurSearch->yega=$v3BidResult->yega;
      $bidpurSearch->innum=$v3BidResult->innum;
      $bidpurSearch->officenm1=$v3BidResult->officenm1;
      $bidpurSearch->prenm1=$v3BidResult->prenm1;
      $bidpurSearch->officeno1=$v3BidResult->officeno1;
      $bidpurSearch->success1=$v3BidResult->success1;
    }

    $bidpurSearch->save();

    gc_collect_cycles();
    $this->stdout(sprintf("[%s] Peak memory usage: %s MB\n",date('Y-m-d H:i:s'),(memory_get_peak_usage(true)/1024/1024)),Console::FG_YELLOW);
  }
}

