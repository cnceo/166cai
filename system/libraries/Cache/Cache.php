<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006 - 2014 EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Caching Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		ExpressionEngine Dev Team
 * @link
 */
class CI_Cache extends CI_Driver_Library {

	protected $valid_drivers 	= array(
		'cache_apc', 'cache_file', 'cache_memcached', 'cache_dummy', 'cache_redis'
	);

	protected $_cache_path		= NULL;		// Path of cache files (if file-based cache)
	protected $_adapter			= 'dummy';
	protected $_backup_driver;
	protected $_dbname          = 'redis';         

	// ------------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param array
	 */
	public function __construct($config = array())
	{
		if ( ! empty($config))
		{
			$this->_initialize($config);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get
	 *
	 * Look for a value in the cache.  If it exists, return the data
	 * if not, return FALSE
	 *
	 * @param 	string
	 * @return 	mixed		value that is stored/FALSE on failure
	 */
	public function get($id)
	{
		return $this->{$this->_adapter}->get($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 *
	 * @param 	string		Unique Key
	 * @param 	mixed		Data to store
	 * @param 	int			Length of time (in seconds) to cache the data
	 *
	 * @return 	boolean		true on success/false on failure
	 */
	public function save($id, $data, $ttl = 60)
	{
		return $this->{$this->_adapter}->save($id, $data, $ttl);
	}
	
	public function hMSet($id, $field)
	{
		return $this->{$this->_adapter}->hMSet($id, $field);
	}
	
	public function hMGet($id, $field) 
	{
		return $this->{$this->_adapter}->hMGet($id, $field);
	}
	
 	public function hSet($id, $field, $value)
    {
    	return $this->{$this->_adapter}->hSet($id, $field, $value);
    }
    
 	public function hGet($id, $field)
    {
    	return $this->{$this->_adapter}->hGet($id, $field);
    }
    
	public function hDel($id, $field)
    {
    	return $this->{$this->_adapter}->hDel($id, $field);
    }
	
	public function hGetAll($id)
	{
		return $this->{$this->_adapter}->hGetAll($id);
	}

	// LIST
	public function Llen($id)
	{
		return $this->{$this->_adapter}->Llen($id);
	}

	public function Lpush($id, $value)
	{
		return $this->{$this->_adapter}->Lpush($id, $value);
	}

	public function Rpop($id)
	{
		return $this->{$this->_adapter}->Rpop($id);
	}
	
	public function increment($id, $offset = 1)
	{
		return $this->{$this->_adapter}->increment($id, $offset);
	}
	
	public function decrement($id, $offset = 1)
	{
		return $this->{$this->_adapter}->decrement($id, $offset);
	}
	
	public function init($msdb = 'redis')
	{
		return $this->{$this->_adapter}->init($msdb);
	}
    /**
     * Redis 数据集操作方法
     * sAdd 追加有序数据集的元素
     */
    public function sAdd($key, $mem)
    {
        return $this->{$this->_adapter}->sAdd($key, $mem);
    }

    public function sMembers($key)
    {
        return $this->{$this->_adapter}->sMembers($key);
    }

    public function sRemove($key, $mem)
    {
        return $this->{$this->_adapter}->sRemove($key, $mem);
    }

    public function sInter($keys){
        return $this->{$this->_adapter}->sInter($keys);
    }

    public function sInterStore($out, $keys){
        return $this->{$this->_adapter}->sInterStore($out, $keys);
    }

    /**
     * Redis 有序数据集操作方法
     * zAdd 追加有序数据集的元素
     */
    public function zAdd($key, $score, $value){
        return $this->{$this->_adapter}->zAdd($key, $score, $value);
    }

    public function zCard($key){
        return $this->{$this->_adapter}->zCard($key);
    }

    public function zRange($key, $start, $stop, $wscore = false){
        return $this->{$this->_adapter}->zRange($key, $start, $stop, $wscore = false);
    }

    /**
     * Redis 有序数据集操作方法
     * zInter 求有序数据集合的交集
     * Parameters: $key keyOutput $arrSet arrayZSetKeys $arrScore arrayWeights
     * $method aggregateFunction Either "SUM", "MIN", or "MAX": defines the behaviour to use on duplicate entries during the zInter
     * Return value:LONG The number of values in the new sorted set
     */
    public function zInter($key, $arrSet, $arrScore=array(), $method=''){
        return $this->{$this->_adapter}->zInter($key, $arrSet, $arrScore=array(), $method='');
    }

    /**
     * Redis 有序数据集操作方法
     * zInter 求有序数据集合的并集
     * Parameters: $key keyOutput $arrSet arrayZSetKeys $arrScore arrayWeights
     * $method aggregateFunction Either "SUM", "MIN", or "MAX": defines the behaviour to use on duplicate entries during the zInter
     * Return value:LONG The number of values in the new sorted set
     */
    public function zUnion($key, $arrSet, $arrScore=array(), $method=''){
        return $this->{$this->_adapter}->zUnion($key, $arrSet, $arrScore=array(), $method='');
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
        return $this->{$this->_adapter}->zRevrange($key, $start, $stop, 'WITHSCORES');
    }

    /**
     * 返回指定成员的排名值  排名从0-逐渐递增
     * @param unknown $key
     * @param unknown $member
     * @return unknown
     */
    public function zRevrank($key, $member)
    {
        return $this->{$this->_adapter}->zRevrank($key, $member);
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
        return $this->{$this->_adapter}->zRemrangebyrank($key, $start, $stop);
    }

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param 	mixed		unique identifier of the item in the cache
	 * @return 	boolean		true on success/false on failure
	 */
	public function delete($id)
	{
		return $this->{$this->_adapter}->delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 *
	 * @return 	boolean		false on failure/true on success
	 */
	public function clean()
	{
		return $this->{$this->_adapter}->clean();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @param 	string		user/filehits
	 * @return 	mixed		array on success, false on failure
	 */
	public function cache_info($type = 'user')
	{
		return $this->{$this->_adapter}->cache_info($type);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		return value from child method
	 */
	public function get_metadata($id)
	{
		return $this->{$this->_adapter}->get_metadata($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Initialize
	 *
	 * Initialize class properties based on the configuration array.
	 *
	 * @param	array
	 * @return 	void
	 */
	private function _initialize($config)
	{
		$default_config = array(
				'adapter',
				'memcached',
				'dbname'
			);

		foreach ($default_config as $key)
		{
			if (isset($config[$key]))
			{
				$param = '_'.$key;

				$this->{$param} = $config[$key];
			}
		}

		if (isset($config['backup']))
		{
			if (in_array('cache_'.$config['backup'], $this->valid_drivers))
			{
				$this->_backup_driver = $config['backup'];
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Is the requested driver supported in this environment?
	 *
	 * @param 	string	The driver to test.
	 * @return 	array
	 */
	public function is_supported($driver)
	{
		static $support = array();
		
		if ( ! isset($support[$driver."_{$this->_dbname}"]))
		{
			$support[$driver."_{$this->_dbname}"] = $this->{$driver}->is_supported($this->_dbname);
		}
		
		return $support[$driver."_{$this->_dbname}"];
	}

	// ------------------------------------------------------------------------

	/**
	 * __get()
	 *
	 * @param 	child
	 * @return 	object
	 */
	public function __get($child)
	{
		$obj = parent::__get($child);

		if ( ! $this->is_supported($child))
		{
			$this->_adapter = $this->_backup_driver;
		}

		return $obj;
	}

}

/* End of file Cache.php */
