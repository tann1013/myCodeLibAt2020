<?php
/**
 * Created by PhpStorm.
 * @author tann1013@hotmail.com
 * @date 2019-07-23
 * @version 1.0
 */

class DateUserfullFunctions
{
    /**
     * 日期区间计算
     * @author tann1013@hotmail.com
     * @date 2019-7-15
     * @param $type
     * @return array
     *  $jList = $this->_getDateRangeListByType('month');//day、week、month
     */
    public function getDateRangeListByType($type){
        $jList = [];
        //day,week,month
        if(in_array($type, ['day', 'week', 'month'])){
            for ($j=-1;$j>=-7;$j--){
                //day
                if($type=='day'){//@todo 日（近7日、从昨日开始计算）
                    $jList[] =  date('Y-m-d', strtotime("$j $type"));
                }

                //week
                if($type=='week'){//@todo 周（近6周、含本周）
                    if($j==-1){
                        $thisCell = [
                            //本周一
                            'monday' => date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)),
                            //本周日
                            'sunday' => date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600)),
                        ];
                        $jList[] =  $thisCell;
                    }

                    if($j>-6 && $j<-1){
                        //上周一
                        $nowTime = strtotime("this monday");
                        $monday =  date('Y-m-d', strtotime("$j monday", $nowTime)); //无论今天几号,-1 monday为上一个有效周未
                        //dd($j, date('Y-m-d H:i:s', $nowTime), $monday);

                        //上周日
                        $sunday = date('Y-m-d', strtotime("$j sunday", $nowTime)); //上一个有效周日,同样适用于其它星期
                        $cell = [
                            'monday' => $monday,
                            'sunday' => $sunday,
                            'j' => $j
                        ];
                        $jList[] =  $cell;
                    }
                }

                //month @todo 月（近6月、含本月）
                if($type=='month'){
                    if($j==-1){

                        $currentMonthFirstDay = date('Y-m', time()) . '-01 00:00:00';
                        $monthRange = $this->_getMonthRange($currentMonthFirstDay);
                        $thisCell = [
                            'firstDay' => $monthRange[0],
                            'lastDay' => $monthRange[1],
                            'j' => $j
                        ];
                        $jList[] =  $thisCell;

                    }
                    if($j>-6){
                        /**
                         * 本月
                         */
                        $currentMonthFirstDay = date('Y-m', time()) . '-01 00:00:00';
                        $currentMonthFirstDayTimeStamp = strtotime($currentMonthFirstDay);
                        //上月一号
                        $currentFirstDay =  date('Y-m-d', strtotime("$j month", $currentMonthFirstDayTimeStamp));

                        $monthRange = $this->_getMonthRange($currentFirstDay);

                        $cell = [
                            'firstDay' => $monthRange[0],
                            'lastDay' => $monthRange[1],
                            'j' => $j
                        ];
                        $jList[] =  $cell;
                    }
                }
            }
        }
        return $jList;
    }

    /**
     * 获得月区间#v1.0
     * @param $date
     * @return array
     */
    private function _getMonthRange($date){
        $firstday = date("Y-m-01",strtotime($date));
        $lastday = date("Y-m-d",strtotime("$firstday +1 month -1 day"));
        return array($firstday,$lastday);
    }

}