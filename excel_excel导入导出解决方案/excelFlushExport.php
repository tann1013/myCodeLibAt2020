/**
 * @param $csvHead  ['ID', '所属团队ID'];
 * @param $list
 * 1、$column_key = ['id','group_id'];
 * 2、$list = array( ['id'=>1,'group_id'=>10],['id'=>2,'group_id'=>10] );
 *
 * @param $title    '云仓报表';
 * @param int $limit
 */
function excelFlushExport($csvHead, $list, $title ,$limit = 5000){
    set_time_limit(0);
    ini_set('memory_limit', '512M');
    $csv_file = $title . '_' . date('Ymd') . '.csv';
    header('Content-Type: application/vnd.ms-excel;charset=gbk');
    header('Content-Disposition: attachment;filename=' . $csv_file);
    header('Cache-Control: max-age=0');
    $fp = fopen('php://output', 'a');
    foreach ($csvHead as $k => $v) {
        $csvHead[$k] = iconv(mb_detect_encoding($v,array("ASCII","UTF-8","GB2312","GBK","BIG5")),'gbk',$v);
    }
    fputcsv($fp, $csvHead);
    $cnt = 0;
    foreach ($list as $lv) {
        $cnt++;
        if ($limit == $cnt) {
            ob_flush();
            flush();
            $cnt = 0;
        }
        foreach ($lv as $ik => $iv) {
            $iv = str_replace("\n", "", str_replace("\r", "", $iv));
            if (is_numeric($iv) && strlen($iv) > 11) {
                $row[$ik] = iconv(mb_detect_encoding($iv,array("ASCII","UTF-8","GB2312","GBK","BIG5")),'gbk',$iv). "\t";
            } else {
                $iv = (string)$iv;
                $row[$ik] = iconv(mb_detect_encoding($iv,array("ASCII","UTF-8","GB2312","GBK","BIG5")),'gbk',$iv);
            }
        }
        fputcsv($fp, $row);
    }
}
