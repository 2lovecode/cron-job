#### 秒级定时任务工具，基于workerman库

###### 以往我们实现定时任务主要依靠在服务器端配置crontab，但这种做法有几个弊端
1) crontab只支持分钟级别，如果要实现秒级别的任务就必须写shell脚本实现。
2) 如果代码部署上线后，需要更改或者添加新的定时任务，就必须登陆到服务器进行手动更改，耗时耗力且易出错。

###### cron-job解决了主要的问题
1) 通过使用秒级定时器(定时器具体实现因安装的扩展不同而有性能差异，但对我们的功能不影响)，实现了秒级别的定时任务。
2) 定时任务的配置可以在配置文件中更改，在代码部署上线后，只要reload定时任务服务就可以，而且reload提供的是平滑重启，不影响正在执行的任务。
3) 支持定时任务分发器和定时任务执行器部署到不同的服务器。

###### 使用示例
```
    1.使用composer安装
    在composer.json文件中的require下添加：
    "require" : {
    		"2lovecode/cron-job": "dev-master"
    }
    执行composer install或composer update即可
    2.test.php
    <?php
    require_once "../vendor/autoload.php";
    
    $configDir = "";//指定配置文件路径,如果配置为空,默认使用default-config.php配置
    
    \CronJob\CronJob::run($configDir);//运行
    
    3.在cli模式下执行
    php test.php start //debug模式运行
    或
    php test.php start -d //守护进程模式运行
    
    4.其它命令
    php test.php stop //停止
    php test.php stop -g
    
    php test.php reload
    php test.php restart
    
    php test.php status
    php test.php status -d
    
    php test.php connections
    
    可以通过php test.php查看命令帮助.
```

###### 配置文件参数 由php文件替换为json文件
```
    {
      "mode" : "both",
    
      "port": "8888",
     
      "host": "127.0.0.1",
      
      "processCount": 4,
      
      "execution-env": "",
      
      "stdout-log-file": "/tmp/cron-job-out.log",
      
      "stderr-log-file": "/tmp/cron-job-err.log",
    
      "cron": {
        "echo \"ccc\"": ["*", "*", "*", "*", "*", "*"]
      }
    }
    
    1.mode : required
       - 指定模式:有3种trigger,actuator,both
       - trigger,在当前服务器运行一个触发器
       - actuator,在当前服务器运行一个执行器
       - both,在当前服务器同时运行触发器和执行器
    
    
    2.port : required
        - 触发器监听的端口
        
    3.host : 执行器地址
        - 执行器地址,仅在trigger和both模式下有效
        
    4.processCount : 
        - 进程数配置,仅在actuator和both模式下有效
        
    5.execution-env :
        - 命令解释环境,为空则为shell命令
        
        
    6.stdout-log-file :
        - 标准输出记录文件,为空则不计录
        
    7.stderr-log-file :
        - 错误输出记录文件,为空则不计录
        
    8.cron : required
        - 具体的定时任务配置,相对于linux的crontab,添加了秒级支持.
```