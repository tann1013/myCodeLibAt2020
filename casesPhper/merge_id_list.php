<?php

/*合并两组id（排重），并默认按id desc排序*/
function merge_id_list($list_a, $list_b, $sort='none') {
    $id_list = array_flip($list_a) + array_flip($list_b);
    if ($sort == 'DESC') {
        krsort($id_list, SORT_NUMERIC);
    } elseif ($sort == 'ASC') {
        ksort($id_list, SORT_NUMERIC);
    }
    return array_keys($id_list);
}

?>

