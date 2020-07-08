<?php
/*
 * @todo 测试守护进程
 * php test.php
 * tail -f test.log
 *
 */
//启用守护进程
function daemonize()
{
    $pid = pcntl_fork();//@see 在当前进程当前位置产生分支（子进程）
    if ($pid == -1)
    {
        die("fork(1) failed!\n");
    }
    elseif ($pid > 0)
    {
        //让由用户启动的进程退出
        exit(0);
    }

    //建立一个有别于终端的新session以脱离终端
    posix_setsid();//@see Make the current process a session leader.

    $pid = pcntl_fork();
    if ($pid == -1)
    {
        die("fork(2) failed!\n");
    }
    elseif ($pid > 0)
    {
        //父进程退出, 剩下子进程成为最终的独立进程
        exit(0);
    }
}

daemonize();//注释daemonize()，则普通进程执行，放开则守护进程执行。

for($i=0;$i<=10;$i++){
    //echo $i.PHP_EOL;
    file_put_contents('./test.log', date('Y-m-d H:i:s', time()). ':' .$i."\n", FILE_APPEND);
    sleep(1);
}

