<?php
/**
 * 十大算法之dfs
 *
 */
class DFS
{
    //无向图的数组描述
    private $dfs_save;
    //全局记录数组
    private $arr;
    //控制分支-
    private $k = 0;
    public function __construct()
    {
        $this->dfs_save = array(
            array(0,1,1,1,0,0,0,0,0),
            array(1,0,0,0,1,0,0,0,0),
            array(1,0,0,0,0,1,0,0,0),
            array(1,0,0,0,0,0,1,0,0),
            array(0,1,0,0,0,1,0,0,1),
            array(0,0,1,0,1,0,0,1,0),
            array(0,0,0,1,0,0,0,0,0),
            array(0,0,0,0,0,1,0,0,0),
            array(0,0,0,0,1,0,0,0,0),
        );
        $this->arr = array();
    }

    /**
     * 深度优先搜索的递归实现方法
     * @param $v
     */
    public function dfs($v)
    {
        //对顶点做一些操作
        echo str_repeat("-",$this->k);
        echo 'V'.($v+1).PHP_EOL;
        //记录已访问的顶点
        $this->arr[]= $v;
        //查找与顶点相连接的顶点，如果存在就继续深度优先搜索
        for($i=0;$i<9;$i++)
        {
            if(!in_array($i,$this->arr)&&$this->dfs_save[$v][$i]==1)
            {
                $this->k++;
                $this->dfs($i);
            }
        }
        $this->k--;
        return;
    }

}

//$obj = new DFS();
//$obj->dfs(2);

?>

