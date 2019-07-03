<?php
/**
 * cron-job
 *
 * @author    liu hao<liu546hao@163.com>
 * @copyright liu hao<liu546hao@163.com>
 */

namespace CronJob\Modes;


use CronJob\CronJob;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Lib\Timer;

abstract class AbstractMode
{
    abstract function config();

    public function onWorkerStart($cronJobServer)
    {
        if ($cronJobServer->id === 0) {
            $trigger = new AsyncTcpConnection("tcp://".CronJob::$host.":".CronJob::$port);
            $trigger->protocol = CronJob::$protocolClass;
            $trigger->connect();
            $timeInterval = 1;
            Timer::add($timeInterval, function () use ($trigger) {
                $nowTime = explode(' ', date('s i G j n w', time()));
                foreach (CronJob::$cronList as $taskName => $timePieces) {
                    $sendFlag = true;
                    foreach ($timePieces as $key => $item) {
                        if (!in_array(intval($nowTime[$key]), $item)) {
                            $sendFlag = false;
                            break;
                        }
                    }
                    if ($sendFlag) {
                        $trigger->send($taskName);
                    }
                }
            });
        }
    }

    public function onMessage($connection, $data)
    {
        if ((CronJob::$processCount === 1) || ($connection->worker->id !== 0)) {
            $outLog = CronJob::$outLog ?? '/dev/null';
            $errorLog = CronJob::$errorLog ?? '&1';
            $command = CronJob::$env.' '.$data.' >> '.$outLog.' 2>>'.$errorLog;
            system($command.' &');
        }
    }

    public function onWorkerReload($worker)
    {
        CronJob::reloadCron();
    }
}