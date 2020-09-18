<?php
/**
 * 消息去重中间件v1.0(数组模拟)
 *
 * @author tann1013@hotmail.com
 * @date 2020-09-18
 * @version 1.0
 */
/***
 * @param $arr
 * @param $pickKey
 * @return array
 */
function _arrayGroupByCellKey($arr, $pickKey)
{
    $result = array();
    foreach ($arr as $k => $v) {
        $result[$v[$pickKey]][] = $v;
    }
    return $result;
}

$newMsgList = [];
//假设这个取的一段数据（5分钟产生的消息数据）
$msgList = array(
    ['code'=>'p100','time'=>1],
    ['code'=>'p100','time'=>2],//picked

    ['code'=>'p200','time'=>3],
    ['code'=>'p201','time'=>4],//picked
    ['code'=>'p200','time'=>7],
    ['code'=>'p200','time'=>8],//picked

    ['code'=>'p300','time'=>11],//picked
);

//1 分组
$msgListGroupByCode = _arrayGroupByCellKey($msgList, 'code');
//print_r($msgListGroupByCode);die;
//2 再遍历
foreach ($msgListGroupByCode as $keyCode=>$itemMsgList){
    $itemMsgListLength = sizeof($itemMsgList);
    if($itemMsgListLength==1){
        $cellMsg = $itemMsgList[0];
    }else{
        $cellMsg = $itemMsgList[$itemMsgListLength-1];
    }

    //赋值到新队列
    array_push($newMsgList, $cellMsg);
}

echo '--msgList--'.PHP_EOL;
print_r($msgList);
echo '--newMsgList--'.PHP_EOL;
print_r($newMsgList);
