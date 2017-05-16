<?php
class redisDB{
	private static $conn = null;
	public function __construct() {
		global $redisInfo;
		extract ( $redisInfo );
		if(self::$conn == null) {
			self::$conn = new Redis();
			try {
				self::$conn->connect($host, $port);
				self::$conn->auth($auth);
			} catch (RedisException $e) {
				echo 'RedisException';
			}
		}
	}

	public static function setWithTimeOut($key, $value, $ttl) {
	    self::$conn->set($key, $value);
	    self::$conn->setTimeout($key, $ttl);
    }

    public static function get($key) {
	    return self::$conn->get($key);
    }

    public static function del($key) {
	    self::$conn->del($key);
    }

    public static function ttl($key) {
	    return self::$conn->ttl($key);
    }

    public static function rpush($key, $value) {
        self::$conn->rpush($key, $value);
    }
}
