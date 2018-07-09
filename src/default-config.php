<?php
/**
 * cron-job
 *
 * @author    liu hao<liu546hao@163.com>
 * @copyright liu hao<liu546hao@163.com>
 */

return [
    //指定模式:有3种trigger,actuator,both
    //trigger,在当前服务器运行一个触发器
    //actuator,在当前服务器运行一个执行器
    //both,在当前服务器同时运行触发器和执行器
    'mode' => 'both',

    //触发器监听的端口
    'port' => '8888',

    //执行器地址,仅在trigger和both模式下有效
    'host' => '127.0.0.1',//执行器地址

    //进程数配置,仅在actuator和both模式下有效
    'processCount' => 4,

    'cron' => [
        'task1' => ['*', '*', '*', '*', '*', '*'],
        'task2' => ['*/2', '*', '*', '*', '*', '*'],
        'task3' => ['1,2', '*', '*', '*', '*', '*'],
        'task4' => ['4-6,7-8', '*', '*', '*', '*', '*'],
    ],

];