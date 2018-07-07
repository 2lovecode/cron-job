<?php
/**
 * cron-job
 *
 * @author    liu hao<liu546hao@163.com>
 * @copyright liu hao<liu546hao@163.com>
 */

namespace CronJob;

use Workerman\Connection\AsyncTcpConnection;
use Workerman\Worker;
use Workerman\Lib\Timer;

class CronJob {
    public static $host = "127.0.0.1";
    public static $port = "8888";
    public static $processCount = 4;
    public static $protocolClass = "Workerman\\Protocols\\Text";

    public static $dimensions = array(
        array(0,59), //Seconds
        array(0,59), //Minutes
        array(0,23), //Hours
        array(1,31), //Days
        array(1,12), //Months
        array(0,6),  //Weekdays
    );

    public static function run ()
    {
        require_once '../vendor/autoload.php';
        
        $cronJobServer = new Worker("tcp://".CronJob::$host.":".CronJob::$port);
        $cronJobServer->protocol = CronJob::$protocolClass;

        $cronJobServer->count = CronJob::$processCount;

        $cronJobServer->onWorkerStart = function($cronJobServer) {
            if ($cronJobServer->id === 0) {
                $trigger = new AsyncTcpConnection("tcp://".CronJob::$host.":".CronJob::$port);
                $trigger->protocol = CronJob::$protocolClass;
                $trigger->connect();
                $cronJobConfig = CronJob::parseConfig();
                $timeInterval = 1;
                Timer::add($timeInterval, function () use ($trigger, $cronJobConfig) {
                    $nowTime = explode(' ', date('s i G j n w', time()));
                    foreach ($cronJobConfig as $taskName => $timePieces) {
                        $sendFlag = true;
                        foreach ($timePieces as $key => $item) {
                            if (!in_array($nowTime[$key], $item)) {
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
        };

        $cronJobServer->onMessage = function ($connection, $data) {
            if ((CronJob::$processCount === 1) || ($connection->worker->id !== 0)) {
                echo $data."\n";
            }
        };

        // 运行worker
        Worker::runAll();
    }


    public static function parseConfig()
    {
        return [
            "task1" => [
                ['01', '02', '03', '04', '05', '14', '15', '16', '29', '30', '31', '44', '45', '46', '56', '57', '58', '59'],
                ['01', '02', '03', '04', '05', '14', '15', '16', '29', '30', '31', '44', '45', '46', '56', '57', '58', '59'],
                ['0', '1', '2', '3'],
                ['1', '2', '3', '7', '8', '9'],
                ['1', '2', '3', '7'],
                ['0', '1', '2', '3', '4', '5', '6'],
            ],
        ];
    }
}

CronJob::run();