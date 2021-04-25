<?php
/***
 *
 *
 */
require_once 'DFS.php';
$obj = new DFS();
$obj->dfs(2);
/*
输出结果如下：
V3
-V1
--V2
---V5
----V6
-----V8
----V9
--V4
---V7
*/
