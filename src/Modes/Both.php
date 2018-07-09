<?php
/**
 * cron-job
 *
 * @author    liu hao<liu546hao@163.com>
 * @copyright liu hao<liu546hao@163.com>
 */

namespace CronJob\Modes;


use CronJob\CronJob;
use Workerman\Worker;

class Both extends AbstractMode
{
    public function config()
    {
        $cronJobServer = new Worker("tcp://".CronJob::$host.":".CronJob::$port);
        $cronJobServer->protocol = CronJob::$protocolClass;

        $cronJobServer->count = CronJob::$processCount;

        $cronJobServer->onWorkerStart = array($this, 'onWorkerStart');

        $cronJobServer->onMessage = array($this, 'onMessage');
    }
}