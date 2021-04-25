mespace App\Http\Controllers\Activity;

use App\Http\Controllers\Controller;
use App\Models\ActParter;
use App\Models\ActPunchLog;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LockController extends Controller
{
    //锁状态#locked、opened、opening待开锁
    const LUCK_ST_LOCKED  = 'locked';
    const LUCK_ST_OPENED  = 'opened';
    const LUCK_ST_OPENING = 'opening';

    //锁所处在的状态#1-用户未连续打卡、2-今天未打卡、3-今天已打卡
    const LOCK_STEP_BREAK_PC = 1;//用户未连续打卡
    const LOCK_STEP_TODAY_NOT_PC = 2;//今天未打卡
    const LOCK_STEP_TODAY_HAD_PC = 3;//今天已打卡
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $now_ymd_his = date('Y-m-d H:i:s', time());
        $now_ymd = date('Y-m-d', time());
        /**
         * 1、读取配置信息
         * //正式：4月1日—4月10日晚20：00截止
         */
        $date_setting = ActivitySetting::_getDateSettingForTest();
        $date_range = $date_setting['ymd'];
        $conf_act_status    = $date_setting['act_status'];

        if($conf_act_status===ActivitySetting::ACT_STATUS_NO){
            $message = '活动未开始！';
            $code = 301;
            return $this->formatResponse(['code'=>$code, 'message'=>$message]);
        }
        if($conf_act_status===ActivitySetting::ACT_STATUS_END){
            $message = '活动已结束！';
            $code = 302;
            return $this->formatResponse(['code'=>$code, 'message'=>$message]);
        }
        /**
         * 2、查询活动日期区间
         */
        $member_id = Auth::user()->id;
        $ymd_list_set = $this->_getYmdListByDateRange($date_range);
        /**
         * 3、自动生成日期锁信息
         */
        $date_lock_list = $this->_getLockListNew($ymd_list_set);

        //活动第一天到昨天的日期区间#$ymd_list_history
        $ymd_list_history = array();
        $ymd_list = $ymd_list_set['ymd_list'];
        foreach ($ymd_list as $ymd){
            if($ymd<$now_ymd){
                $ymd_list_history[] = $ymd;
            }
        }
        /**
         * 第一天到今天的全部打卡信息
         */
        //从活动第一天到今天期间总打卡数量
        $punch_id_list = ActPunchLog::getPcIdsByMid($member_id);
        $all_punch_count = sizeof($punch_id_list);
        //活动已经进行的天数
        $current_days  = (int)sizeof($ymd_list_history)+1;
        $minus_ret = $current_days - $all_punch_count;
        if($minus_ret>=2){
            //缺卡
            $message = '用户未连续打卡！';
            $_data = [
                'act_status' => $conf_act_status,
                'lock_step'  => self::LOCK_STEP_BREAK_PC,//用户未连续打卡
            ];
            return $this->formatResponse([ 'message'=>$message, 'data'=>$_data]);
        }
        if($minus_ret == 1){
            $lock_step =  self::LOCK_STEP_TODAY_NOT_PC;//今天未打卡
        }
        if($minus_ret == 0 ){
            $lock_step = self::LOCK_STEP_TODAY_HAD_PC;//今天已打卡
        }
        
        foreach ($date_lock_list as $k=>&$item){
            if($item['lock_ymd']<$now_ymd){
                $item['lock_st'] = self::LUCK_ST_OPENED;
                $item['lock_punch_id'] = isset($punch_id_list[$k]) ?  $punch_id_list[$k]: 0;
            }elseif($item['lock_ymd']==$now_ymd){
                if($lock_step === self::LOCK_STEP_TODAY_HAD_PC){//今天已打卡
                    $item['lock_st'] = self::LUCK_ST_OPENED;
                    $item['lock_punch_id'] = isset($punch_id_list[$k]) ?  $punch_id_list[$k]: 0;
                }else{
                    $item['lock_st'] = self::LUCK_ST_OPENING;
                }
            }
        }

        $_data = [
            'act_status' =>$conf_act_status,
            'lock_step' => $lock_step,
            'list' => $date_lock_list
        ];

        return $this->formatResponse(array(
            'data' => $_data
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function _getLockListNew($ymd_list_set)
    {
        $ymd_list = $ymd_list_set['ymd_list'];
        $ymd_days = $ymd_list_set['days'];
        //生成日期锁
        $date_lock_list = array();
        if($ymd_list){
            foreach ($ymd_list as $k=>$ymd){
                $lock_num = $ymd_days-$k-1;
                $lock_name = 'lock_'.$lock_num;
                $date_lock_list[] = [
                    'lock_st'   => self::LUCK_ST_LOCKED,
                    'lock_ymd'  => $ymd,
                    'lock_name' => $lock_name,
                    'lock_punch_id' => 0,
                ];
            }
        }
        return $date_lock_list;
    }

    /**
     * @param array $range['start] $range['end]
     */
    private function _getYmdListByDateRange(array $date_range){
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
}

