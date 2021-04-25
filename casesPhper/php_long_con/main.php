<?php
/*
 * PHP中长连接的实现
 *
 * 每次我们访问PHP脚本的时候，都是当所有的PHP脚本执行完成后，
 * 我们才得到返回结果。如果我们需要一个脚本持续的运行，
 * 那么我们就要通过php长连接的方式，来达到运行目的。
 *
 *
 * 我们执行后，每隔5秒钟，我们会得到一行 Hello World ，如果不按停止按钮，浏览器会不停的一行一行继续加载。
 * 通过这一方法，我们可以完成很多功能，例如机器人爬虫、即时留言板等程序
 *
 *
 *
 *
 *
 */
header("Content-Type: text/plain");
set_time_limit(10);//开始计时  //set_time_limit()函数来设置PHP页面的最大运行时间
ob_start();

$infoString = "Hello World" . "\n";
while( isset($infoString) )
{
    echo $infoString;
    flush();
    ob_flush();
    sleep(5);
}