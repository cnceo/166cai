<?php
/**
* CodeIgniter
*
* An open source application development framework for PHP 5.2.4 or newer
*
* NOTICE OF LICENSE
*
* Licensed under the Open Software License version 3.0
*
* This source file is subject to the Open Software License (OSL 3.0) that is
* bundled with this package in the files license.txt / license.rst. It is
* also available through the world wide web at this URL:
* http://opensource.org/licenses/OSL-3.0
* If you did not receive a copy of the license and are unable to obtain it
* through the world wide web, please send an email to
* licensing@ellislab.com so we can send you a copy immediately.
*
* @package CodeIgniter
* @author EllisLab Dev Team
* @copyright Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
* @license http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
* @link http://codeigniter.com
* @since Version 3.0
* @filesource
* 
* 
$this->load->driver('cache', array('adapter' => 'redis'));
$redis= $this->cache->redis;
$redis->test_redis();
这样可以在redis类中直接添加方法
如果该方法不存在调用CI_Driver类的__call()
CI_Driver 的父类是被设置成CI_Cache的
$this->cache->save('soho', $soho, 300);
以上是通用的方法同时需要在CI_Cache类中添加方法
*/
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* CodeIgniter Redis Caching Class
*
* @package CodeIgniter
* @subpackage Libraries
* @category Core
* @author Anton Lindqvist <anton@qvister.se>
* @link
*/
class CI_Cache_redis extends CI_Driver
{
	/**
	* Default config
	*
	* @static
	* @var array
	*/
	protected static $_default_config ;
	/**
	* Redis connection
	*
	* @var Redis
	*/
	protected $_redis;
	/**
	* An internal cache for storing keys of serialized values.
	*
	* @var array
	*/
	protected $_serialized = array();
	
	/**
	 * 连接标识
	 * @var unknown_type
	 */
	protected $_selectDb;
	
	public function __construct()
	{
		$CI = &get_instance();
		$CI->config->load('redis');
		self::$_default_config = $CI->config->item('redis');
	}
	// ------------------------------------------------------------------------
	/**
	* Get cache
	*
	* @param string Cache ID
	* @return mixed
	*/
	public function get($key)
	{
		//如果从库判断过期时间
		if($this->_selectDb == 'slave')
		{
			if(in_array($this->_redis->ttl($key), array('0', '-2')))
			{
				return ;
			}
		}
		$value = $this->_redis->get($key);
		/*if ( $value !== FALSE && $this->_redis->sIsMember('_ci_redis_serialized', $key) )
		{
			return unserialize($value);
		}*/
		return $value;
	}
	// ------------------------------------------------------------------------
	/**
	* Save cache
	*
	* @param string $id Cache ID
	* @param mixed $data Data to save
	* @param int $ttl Time to live in seconds
	* @param bool $raw Whether to store the raw value (unused)
	* @return bool TRUE on success, FALSE on failure
	*/
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		/*if (is_array($data) OR is_object($data))
		{
			$this->_redis->sAdd('_ci_redis_serialized', $id);
			//isset($this->_serialized[$id]) OR $this->_serialized[$id] = TRUE;
			$data = serialize($data);
		}
		elseif (isset($this->_serialized[$id]))
		{
			$this->_serialized[$id] = NULL;
			$this->_redis->sRemove('_ci_redis_serialized', $id);
		}*/
		if($ttl > 0)
		{
			return $this->_redis->setex($id, $ttl, $data);
		}
		else 
		{
			return $this->_redis->set($id, $data);
		}
	}
	// ------------------------------------------------------------------------
	/**
	* Delete from cache
	*
	* @param string Cache key
	* @return bool
	*/
	public function delete($key)
	{
		if ($this->_redis->delete($key) !== 1)
		{
			return FALSE;
		}
		/*if ($this->_redis->sIsMember('_ci_redis_serialized', $key))
		{
			$this->_redis->sRemove('_ci_redis_serialized', $key);
		}*/
		return TRUE;
	}
	// ------------------------------------------------------------------------
	/**
	* Increment a raw value
	*
	* @param string $id Cache ID
	* @param int $offset Step/value to add
	* @return mixed New value on success or FALSE on failure
	*/
	public function increment($id, $offset = 1)
	{
		return $this->_redis->incr($id, $offset);
	}
	// ------------------------------------------------------------------------
	/**
	* Decrement a raw value
	*
	* @param string $id Cache ID
	* @param int $offset Step/value to reduce by
	* @return mixed New value on success or FALSE on failure
	*/
	public function decrement($id, $offset = 1)
	{
		return $this->_redis->decr($id, $offset);
	}
	// ------------------------------------------------------------------------
	/**
     *  返回哈希值
     * @param string $key KEY名称 
     * @param string  $field 字段名
    */
    public function hGet($key,$field)
    {
    	return $this->_redis->hGet($key,$field);
    }
    // ------------------------------------------------------------------------
 	/**
     * 设置过期时间, 单位 秒
     * @param unknown_type $key
     * @param unknown_type $seconds
     */
    public function setTimeout($key, $seconds)
    {
    	return $this->_redis->setTimeout($key, $seconds);
    }
    // ------------------------------------------------------------------------
    /**
     *  设置哈希值
     * @param string $key KEY名称 
     * @param string  $field 字段名
     * @param string  $value 字段名
    */
    public function hSet($key,$field,$value)
    {
    	return $this->_redis->hSet($key,$field,$value);
    }
    // ------------------------------------------------------------------------
    /**
     * 根据数组设置hash的多个字段值
     * @param string $key KEY名称
     * @param array  $field  字段键值对
     */
    public function hMSet($key, $field) {
        return $this->_redis->hMSet($key, $field);
    }
    // ------------------------------------------------------------------------
    /**
     * 获取指定键里面的多个字段
     * @param string $key KEY名称
     * @param array  $field 字段数组
     */
    public function hMGet($key, $field) {
        return $this->_redis->hMGet($key, $field);
    }
    // ------------------------------------------------------------------------
	 /**
     *  删除哈希值
     * @param string $key KEY名称 
     * @param string  $field 字段名
    */
	public function hDel($key,$field)
	{
		return $this->_redis->hDel($key,$field);
	}	
	// ------------------------------------------------------------------------
    /**
     * 哈希值 自增
     * @param unknown_type $key
     * @param unknown_type $field
     * @param unknown_type $incr
     */
    public function hIncrBy($key, $field, $incr)
    {
    	return $this->_redis->hIncrBy($key,$field,$incr);
    }
    // ------------------------------------------------------------------------
    /**
     * 哈希   返回key下面的所有 $field => $value
     * @param unknown_type $key
     */
    public function hGetAll($key)
    {
    	return $this->_redis->hGetAll($key);
    }

    // ------------------------------------------------------------------------
    /**
     * LIST 列表操作
     */
    public function Llen($id)
    {
        return $this->_redis->Llen($id);
    }

    public function Lpush($key, $value)
    {
        return $this->_redis->Lpush($key, $value);
    }

    public function Rpop($key)
    {
        return $this->_redis->Rpop($key);
    }
    // ------------------------------------------------------------------------
	/**
	* Clean cache
	*
	* @return bool
	* @see Redis::flushDB()
	*/
	public function clean()
	{
		return $this->_redis->flushDB();
	}
	// ------------------------------------------------------------------------
	/**
	* Get cache driver info
	*
	* @param string Not supported in Redis.
	* Only included in order to offer a
	* consistent cache API.
	* @return array
	* @see Redis::info()
	*/
	public function cache_info($type = NULL)
	{
		return $this->_redis->info();
	}
	// ------------------------------------------------------------------------
	/**
	* Get cache metadata
	*
	* @param string Cache key
	* @return array
	*/
	public function get_metadata($key)
	{
		$value = $this->get($key);
		if ($value)
		{
			return array(
			'expire' => time() + $this->_redis->ttl($key),
			'data' => $value
			);
		}
		return FALSE;
	}
	// ------------------------------------------------------------------------
	/**
	* Check if Redis driver is supported
	*
	* @return bool
	*/
	public function is_supported($db='')
	{
		if (extension_loaded('redis'))
		{
			return $this->_setup_redis($db);
		}
		else
		{
			log_message('debug', 'The Redis extension must be loaded to use Redis cache.');
			return FALSE;
		}
	}
	// ------------------------------------------------------------------------
	/**
	* Setup Redis config and connection
	*
	* Loads Redis config file if present. Will halt execution
	* if a Redis connection can't be established.
	*
	* @return bool
	* @see Redis::connect()
	*/
	protected function _setup_redis($msdb = 'redis')
	{
		$config = array();
		$CI =& get_instance();
		if ($CI->config->load('redis', TRUE, TRUE))
		{
			$config += $CI->config->item($msdb);
		}
		$config = array_merge(self::$_default_config, $config);
		$this->_redis = new Redis();
		$this->_selectDb = $msdb;
		try
		{
			if ($config['socket_type'] === 'unix')
			{
				$success = $this->_redis->connect($config['socket']);
			}
			else // tcp socket
			{
				$success = $this->_redis->connect($config['host'], $config['port'], $config['timeout']);
			}
			if ( ! $success)
			{
				log_message('debug', 'Cache: Redis connection refused. Check the config.');
				return FALSE;
			}
		}
		catch (RedisException $e)
		{
			log_message('debug', 'Cache: Redis connection refused ('.$e->getMessage().')');
			return FALSE;
		}
		if (isset($config['password']))
		{
			$this->_redis->auth($config['password']);
			$this->_redis->select(1);
		}
		// Initialize the index of serialized values.
		/*$serialized = $this->_redis->sMembers('_ci_redis_serialized');
		if ( ! empty($serialized))
		{
			$this->_serialized = array_flip($serialized);
		}*/
		return TRUE;
	}
	// ------------------------------------------------------------------------
	public function sAdd($key, $mem)
	{
		return $this->_redis->sAdd($key, $mem);
	}
	
	public function sMembers($key)
	{
		return $this->_redis->sMembers($key);
	}
	
	public function sRemove($key, $mem)
	{
		return $this->_redis->sRemove($key, $mem);
	}

    public function sInter($keys){
        return call_user_func_array(array( $this->_redis, 'sInter'), $keys);
    }

    public function sInterStore($out, $keys){
        array_unshift($keys, $out);
        return call_user_func_array(array( $this->_redis, 'sInterStore'), $keys);
    }
	
	public function multi()
	{
		$this->_redis->multi(Redis::PIPELINE);
	}
	
	public function exec()
	{
		$this->_redis->exec();
	}
	
	public function init($msdb='redis')
	{
		$this->_redis->close();
		$this->_setup_redis($msdb);
	}
	
	/**
     * Redis 有序数据集操作方法
     * zAdd 追加有序数据集的元素
     */
    public function zAdd($key, $score, $value){
        return $this->_redis->zAdd($key, $score, $value);
    }

    public function zCard($key){
        return $this->_redis->zCard($key);
    }

    public function zRange($key, $start, $stop, $wscore = false){
        return $this->_redis->zRange($key, $start, $stop, $wscore = false);
    }

    /**
     * Redis 有序数据集操作方法
     * zInter 求有序数据集合的交集
     * Parameters: $key keyOutput $arrSet arrayZSetKeys $arrScore arrayWeights
     * $method aggregateFunction Either "SUM", "MIN", or "MAX": defines the behaviour to use on duplicate entries during the zInter
     * Return value:LONG The number of values in the new sorted set
     */
    public function zInter($key, $arrSet, $arrScore=array(), $method=''){
        if(!empty($arrScore)){
            return $this->_redis->zInter($key, $arrSet, $arrScore);
        }elseif (!empty($method)){
            return $this->_redis->zInter($key, $arrSet, $arrScore, $method);
        }else{
            return $this->_redis->zInter($key, $arrSet);
        }

    }

    /**
     * Redis 有序数据集操作方法
     * zInter 求有序数据集合的并集
     * Parameters: $key keyOutput $arrSet arrayZSetKeys $arrScore arrayWeights
     * $method aggregateFunction Either "SUM", "MIN", or "MAX": defines the behaviour to use on duplicate entries during the zInter
     * Return value:LONG The number of values in the new sorted set
     */
    public function zUnion($key, $arrSet, $arrScore=array(), $method=''){
        if(!empty($arrScore)){
            return $this->_redis->zUnion($key, $arrSet, $arrScore);
        }elseif (!empty($method)){
            return $this->_redis->zUnion($key, $arrSet, $arrScore, $method);
        }else{
            return $this->_redis->zUnion($key, $arrSet);
        }

    }
	
	/**
	 * 返回指定排名区间内所有成员
	 * @param unknown $key
	 * @param unknown $start
	 * @param unknown $stop
	 * @return unknown
	 */
	public function zRevrange($key, $start, $stop)
	{
	    return $this->_redis->ZREVRANGE($key, $start, $stop, 'WITHSCORES');
	}
	
	/**
	 * 返回指定成员的排名值  排名从0-逐渐递增
	 * @param unknown $key
	 * @param unknown $member
	 * @return unknown
	 */
	public function zRevrank($key, $member)
	{
	    return $this->_redis->ZREVRANK($key, $member);
	}
	
	/**
	 * 移除指定排名区间的成员
	 * @param unknown $key
	 * @param unknown $start
	 * @param unknown $stop
	 * @return unknown
	 */
	public function zRemrangebyrank($key, $start, $stop)
	{
	    return $this->_redis->ZREMRANGEBYRANK($key, $start, $stop);
	}
	
	/**
	* Class destructor
	*
	* Closes the connection to Redis if present.
	*
	* @return void
	*/
	public function __destruct()
	{
		if ($this->_redis)
		{
			$this->_redis->close();
		}
	}
}
/* End of file Cache_redis.php */
/* Location: ./system/libraries/Cache/drivers/Cache_redis.php */
