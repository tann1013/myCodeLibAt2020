<?php
/**
 * Created by PhpStorm.
 * @author tann1013@hotmail.com
 * @date 2019-08-02
 * @version 1.0
 */

namespace App\Http\Controllers\TestSet;

use Illuminate\Support\Facades\Log;

/**
 *
 * PHP中的数据库 工具类设计
 * 郭璞
 * 2016年12月23日
 *
 * Class DbHelper
 * @package App\Http\Controllers\TestSet
 *
 */
class DbPool
{
    private $dbconfig;//数据库配置信息
    private $dbpool;//连接池容器
    public $poolsize;//连接池长度
    public $utilsPath;//配置文件路径

    public function __construct($poolsize = 5) {
        //1 加载配置文件#utils.php
        $utilsPath = app_path()."/Http/Controllers/TestSet/utils.php";
        if (! file_exists ( $utilsPath )) {
            throw new \Exception( "<mark>utils.php文件丢失，无法进行配置文件的初始化操作！</mark><br />" );
        }else {
            require "$utilsPath";
        }

        //2 加载数据库配置信息
        $this->dbconfig = XmlUtil::getDBConfiguration ();

        //3 准备好数据库连接池"伪队列"
        //3.1 初始化连接池长度#$poolsize
        $this->poolsize = $poolsize;

        //3.2 初始化连接池容器#dbpool(模拟为数组)
        $this->dbpool = array ();

        //3.3 初始化5个连接大小的连接池
        for($index = 1; $index <= $this->poolsize; $index ++) {
            $conn = mysqli_connect( $this->dbconfig ['host'], $this->dbconfig ['user'], $this->dbconfig ['password'], $this->dbconfig ['db'] ) or die ( "<mark>连接数据库失败！</mark><br />" );
            array_push ( $this->dbpool, $conn );
        }
    }

    /**
     * 从数据库连接池中获取一个数据库链接资源
     *
     * @throws ErrorException
     * @return mixed
     */
    public function getConn() {
        Log::error('$this->dbpool:'.sizeof($this->dbpool));

        if (count ( $this->dbpool ) <= 0) {
            throw new \Exception( "<mark>数据库连接池中已无链接资源，请稍后重试!</mark>" );
        } else {
            return array_pop ( $this->dbpool );
        }
    }

    /**
     * 将用完的数据库链接资源放回到数据库连接池
     *
     * @param unknown $conn
     * @throws ErrorException
     */
    public function releaseConn($conn) {
        //$p1 = $this->dbpool;
        if (count ( $this->dbpool ) >= $this->poolsize) {
            throw new \ErrorException ( "<mark>数据库连接池已满</mark><br />" );
        } else {
            array_push ( $this->dbpool, $conn );
        }
        //$p2 = $this->dbpool;
        //dd($p1, $p2);
    }
}

function linkPonl(){
    //1 读取配置文件
    //$content = file_get_contents($this->path);
    //dd($content);

    //2 常规方式
    //$handle = fopen($this->path, "r");
    //$content = fread($handle, filesize($this->path));
    //dd($content);
    //dd(XmlUtil::getDBConfiguration());

    //3 连接一个数据库池
    $hp = new DbPool();
    $link = $hp->getConn();
    $link = $hp->getConn();
    $link = $hp->getConn();
    $link = $hp->getConn();
    $link = $hp->getConn();
    //$link = $hp->getConn();
    $hp->releaseConn($link);//释放连接，否则下次将占满
    $link = $hp->getConn();
    dd($link);
}

