<?php
function _writeLog($content){
    $path =  __DIR__ . '/../logs/events-'.date('Y-m-d').'.log';
    file_put_contents($path, ''.date('Y-m-d H:i:s', time()).' '.$content.PHP_EOL, FILE_APPEND);
}

function mainProcess(){
    try {

        if(rand(0,1)){
            throw new Exception('连接redis错误！');
        }

        _writeLog('监控中...');

        while (1){
            _writeLog('2222');

            if(rand(0,1)){
                throw new Exception('连接redis错误！');
            }
        }
    }catch (\Exception $e) {
        //echo $e->getMessage(), PHP_EOL;
        //_writeLog('client_for_entry_err:'.$e->getMessage());
        //@todo 新增连接Canal服务异常重试机制
        sleep(2);

        _writeLog('服务异常重试中...');
        mainProcess();

        //_retry();
    }
}

mainProcess();
