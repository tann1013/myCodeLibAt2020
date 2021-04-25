/**
 * redis单例设计v1
 * @author tann1013@hotmail.com
 * @date 2020-09-18
 * @version 1.0
 */
class RedisClient
{
    public $version = 'v1';
    public $redis;
    public $handle = NULL;
    private static $_instance = NULL;//定义私有的属性变量

    //定义公用的静态方法
    public static function getInstance() {
        if (NULL == self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function __construct() {
        $redis = new Redis();//实例化redis
        $redis->connect('127.0.0.1', 6379);
        //$redis->auth(Conf::AUTH);
        $this->handle = &$redis;
        //将变量与redis通过引用符关联在一起，以后直接使用handle即可，相当于将redis付给一个变量，这是另一种写法
    }

    public function __destruct() {
        $this->handle->close();
    }
}
