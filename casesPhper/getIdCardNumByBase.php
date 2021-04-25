    /**
     * 产生身份证编号
     * baseNum+派出所编号+性别+校验码
     * eg.42130219870907xxx0
     * @return array
     */
    public function getIdCardNumByBase( $base = '42130219870907'){
        
        //派出所编号
        $police_list = [];
        for($i = 1 ;$i<99 ; $i++){
            if(strlen($i) == 1){
                $police_list[] = "00".$i;
            }
            if(strlen($i) == 2){
                $police_list[] = "0".$i;
            }
            if(strlen($i) == 3){
                $police_list[] = $i;
            }
        }
        $police_list = [
            49,//淮河镇
            //48,//小林镇
        ];

        //男女编号
        $sex_list =  [0,2,4,6,8];//女
        //$sex_list =  [0,1,2,3,4,5,6,7,8,9];
        //校验码(0-9,x)
        $code_list =  [0,1,2,3,4,5,6,7,8,9,'x'];//x

        $idList = [];
        foreach ($police_list as $item){
            //base+派出所编号+性别+校验码
            foreach ($sex_list as $sex){
                foreach ($code_list as $code){
                    $id_card = $base.$item.$sex.$code;
                    $idList[] = $id_card;
                }
            }
        }
        return $idList;
    }
