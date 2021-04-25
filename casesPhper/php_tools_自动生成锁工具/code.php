<?php

/**
 *  自动生成锁工具v1.0
 *  @author tann103@hotmail.com
    @date 2019-03-04
 */
class LockController 
{
    //锁状态#locked、open
    const LUCK_ST_LOCKED = 'locked';
    const LUCK_ST_OPEN = 'open';
    //打卡状态#yes、no
    const PUNCH_ST_YES = 'yes';
    const PUNCH_ST_NO = 'no';
    //活动状态 no-未开始,ing-进行中,end-已结束
    const ACT_STATUS_NO = 'no';//未开始
    const ACT_STATUS_ING = 'ing';//进行中
    const ACT_STATUS_END = 'end';//已结束

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * 1、读取配置信息
         *  活动3月10号到3月19号19:00之前//正式：4月1日—4月10日晚20：00截止
         */
        $date_range = array(
            //'start' => '2019-04-11', 'end' => '2019-04-12',//情况1、活动未开始
            'start' => '2019-03-14', 'end' => '2019-03-25',//情况2、活动进行中
            //'start' => '2019-03-01', 'end' => '2019-03-02',//情况3、活动已结束
        );
        $member_id = 2016;
        /**
         * 2、查询活动日期区间
         */
        $ymd_list_set = $this->_getYmdListByDateRange($date_range);
        /**
         * 3、自动自动生成锁信息
         */
        $data_list_set = $this->_getLockList($ymd_list_set, $member_id);
        return $this->formatResponse(array(
            'data'       => $data_list_set['data_lock_list'],
            'act_status' =>$data_list_set['act_status']
        ));
    }

    /**
     * @param $ymd_list_set
     * @param $member_id
     * @return array
     */
    private function _getLockList($ymd_list_set, $member_id){
        $ymd_list  = $ymd_list_set['ymd_list'];
        $ymd_days  = $ymd_list_set['days'];
        /**
         * 生成锁#$lock_list
         */
        $lock_info = array(
            'luck_st'  => self::LUCK_ST_LOCKED,//locked,open
            'punch_st' => self::PUNCH_ST_NO,//yes、no
        );
        $lock_list = array();
        for ($i=$ymd_days-1;$i>=0;$i--){
            $lock_name = 'lock_'.$i;
            $lock_list[$lock_name] = $lock_info;
        }
        /**
         * 2、计算当前用户的打卡信息
         *  假设该用户在之前都是正常打卡
         */
        $ymd_list_history = array();//['2019-03-12', '2019-03-13']
        $ymd_list_now = array();//['2019-03-14']
        $ymd_list_future = array();//['2019-03-15', '2019-03-16']
        $act_status = '';
        //今天日期
        $act_date_today = date('Y-m-d',time());//2019-03-13
        //今天日期下标
        $flag_key = array_search($act_date_today, $ymd_list);
        if($flag_key == false && $flag_key !== 0){
            $ymd_list_max_cell = $ymd_list[$ymd_days-1];
            $ymd_list_min = $ymd_list[0];
            if($act_date_today>$ymd_list_max_cell){
                //活动已结束
                $act_status = self::ACT_STATUS_END;
                $ymd_list_history = $ymd_list;
            }
            if($ymd_list_min>$act_date_today){
                //活动未开始
                $act_status = self::ACT_STATUS_NO;
                $ymd_list_future = $ymd_list;
            }
            //var_dump($act_date_today,$ymd_list);die;
        }else{
            $act_status = self::ACT_STATUS_ING;
            foreach ($ymd_list as $kk=>$item){
                if($kk<$flag_key){
                    array_push($ymd_list_history, $item);
                }elseif($kk == $flag_key){
                    array_push($ymd_list_now, $item);
                }elseif($kk > $flag_key){
                    array_push($ymd_list_future, $item);
                }
            }
        }
        //var_dump($ymd_list, $act_status);die;
        //var_dump($ymd_list_history,$ymd_list_now,$ymd_list_future, $act_status);die;
        /**
         * 初始化锁号码
         */
        $lock_num_current = $ymd_days-1;
        /**
         * 1、查询历史锁信息#$data_lock_list_left
         *  (已结束或者正在进行中的活动)
         */
        $data_lock_list_left = array();
        if(!empty($ymd_list_history)){
            foreach ($ymd_list_history as $j=>$item_before){
                $lock_cell = $this->_getPunchStatusByDate($item_before);
                array_push($data_lock_list_left, $lock_cell);
            }
        }
        /**
         * 2、查询今天的锁信息#$data_lock_list_today
         */
        $data_lock_list_today = array();
        if(!empty($ymd_list_now)){
            foreach ($ymd_list_now as $item_now){
                $lock_cell = $this->_getPunchStatusByDate($item_now);
                array_push($data_lock_list_today, $lock_cell);
            }
        }
        /**
         * 3、查询未来的打卡信息##$data_lock_list_right
         */
        $data_lock_list_right = array();
        if(!empty($ymd_list_future)){
            foreach ($ymd_list_future as $g=>$item_future){
                //当前锁号
                $lock_info = [
                    'luck_st'   => self::LUCK_ST_LOCKED,
                    'punch_st'  => self::PUNCH_ST_NO,
                    'lock_ymd'  => $item_future,
                ];
                array_push($data_lock_list_right, $lock_info);
            }
        }
        $data_lock_list = array_merge($data_lock_list_left, $data_lock_list_today, $data_lock_list_right);
        /**
         * 给锁加序列号
         */
        if($data_lock_list){
            foreach ($data_lock_list as $g=>$cell){
                $lock_num = $ymd_days-$g-1;
                $lock_name = 'lock_'.$lock_num;
                $data_lock_list[$g]['lock_name'] = $lock_name;
            }
        }
        $data_lock_list_set = [
            'data_lock_list' => $data_lock_list,
            'act_status'     => $act_status
        ];
        return $data_lock_list_set;
    }

    /**
     * @param array $range['start] $range['end]
     */
    public function _getYmdListByDateRange(array $date_range){
        $ymd_list = array();
        $start = $date_range['start'];//y-m-d
        $end = $date_range['end'];//y-m-d
        if($end>$start){
            //相差天数
            $d1 = strtotime($start);
            $d2 = strtotime($end);
            $days = round(($d2-$d1)/3600/24);
            for ($j=0; $j<=$days; $j++){
                $lock_ymd = date(
                    "Y-m-d",strtotime("+$j day",strtotime($start))
                );
                array_push($ymd_list, $lock_ymd);
            }
            return  [
                'ymd_list' => $ymd_list,
                'days' => sizeof($ymd_list)
            ];
        }else{
            exit('日期起始设置有误！'.__FUNCTION__);
        }
    }

    /**
     * 查询区间打卡情况
     * @param $ymd  查询日期 2018-10-01
     * @param $lock_num_current 锁号
     * @return array
     */
    private function _getPunchStatusByDate($ymd){
        $s = $ymd . ' 00:00:00';
        $e = $ymd . ' 23:59:59';
        $_where = [
            array( 'created_at', '>=', $s),
            array( 'created_at', '<=', $e),
        ];
        $is_exists = DB::table('act_punch_log')->where($_where)->exists();
        if($is_exists){//已打卡
            $punch_st = self::PUNCH_ST_YES;
        }else{
            $punch_st = self::PUNCH_ST_NO;
        }
        $lock_info = [
            'luck_st'   => 'locked',
            'punch_st'  => $punch_st,
            'lock_ymd'  => $ymd,
        ];
        return $lock_info;
    }
}

