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

class Trigger extends AbstractMode
{
    public function config()
    {
        $cronJobServer = new Worker();
        $cronJobServer->protocol = CronJob::$protocolClass;
        $cronJobServer->reloadable = false;
        $cronJobServer->cronList = CronJob::$cronList;

        $cronJobServer->count = 1;

        $cronJobServer->onWorkerStart = array($this, 'onWorkerStart');
        $cronJobServer->onWorkerReload = array($this, 'onWorkerReload');
    }
}