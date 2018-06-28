<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Stomp {

    /**
     * Default config
     *
     * @static
     * @var array
     */
    protected $_default_config ;
    /**
     * stomp connection
     *
     * @var stomp
     */
    protected $_stomp;
    
	/**
	 * 传配置信息时用传的，否则用默认配置
	 */
	public function __construct($config = array())
	{
	    if($config)
	    {
	        $this->_default_config = $config;
	    }
	    else
	    {
	        $CI = &get_instance();
	        $CI->config->load('stomps');
	        $this->_default_config = $CI->config->item('stomp');
	    }
	}

	/**
	 * stomp Connect
	 *
	 */
	public function connect()
	{
	    try 
	    {
	        $this->_stomp = new Stomp($this->_default_config['url'], $this->_default_config['user'], $this->_default_config['password']);
	        if(!$this->_stomp)
	        {
	            $this->setLogs('error', "Connection failed: " . stomp_connect_error());
	            return false;
	        }
	        
	        return true;
	    } 
	    catch(StompException $e) 
	    {
	        $this->setLogs('error', $e->getMessage());
	        return false;
	    }
	}
	
	public function disconnect()
	{
	    unset($this->_stomp);
	    return true;
	}

	/**
	 * Validates the connection ID
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _is_conn()
	{
	    if (!$this->_stomp)
		{
			$this->setLogs('error', 'stomp_no_connection');
			return false;
		}
		
		return true;
	}
	
	/**
	 * 入队
	 * @param string $destination  队列名称
	 * @param array $data          消息内容  数组
	 * @param array $headers       附加参数
	 * @return boolean|unknown
	 */
	public function push($destination, $data = array(), $headers = array())
	{
	    if ($destination == '' OR ! $this->_is_conn())
	    {
	        return false;
	    }
	    
	    return $this->_stomp->send($destination, json_encode($data), $headers);
	}
	
	/**
	 * 消息读取
	 * @return boolean|unknown
	 */
	public function pop()
	{
	    if (! $this->_is_conn())
	    {
	        return false;
	    }
	    
	    $frame = $this->_stomp->readFrame();
	    if( $frame ) 
	    {
	        if( $frame->command == "MESSAGE" ) 
	        {
	            $frame->body = json_decode($frame->body, true);
	            return $frame;
	        }
	        else
	        {
	            $this->setLogs('error', json_encode($frame));
	        }
	    }
	    
	    return false;
	}
	
	/**
	 * 通知队列删除消息
	 * @param unknown $frame
	 * @return boolean
	 */
	public function ack($frame)
	{
	    if (! $this->_is_conn())
	    {
	        return false;
	    }
	    
	    return $this->_stomp->ack($frame);
	}
	
	/**
	 * 订阅队列消息
	 * @param string $destination  队列名称
	 * @param array $headers
	 * @return boolean
	 */
	public function subscribe($destination, $headers = array())
	{
	    if ($destination == '' OR ! $this->_is_conn())
	    {
	        return false;
	    }
	    
	    return $this->_stomp->subscribe($destination, $headers);
	}
	
	/**
	 * 订阅取消
	 * @param unknown $destination
	 * @param array $headers
	 * @return boolean
	 */
	public function unsubscribe($destination, $headers = array())
	{
	    if ($destination == '' OR ! $this->_is_conn())
	    {
	        return false;
	    }
	    
	    return $this->_stomp->unsubscribe($destination, $headers);
	}
	
	/**
	 * 队列是否有消息可读
	 * @return boolean|unknown
	 */
	public function hasPop()
	{
	    if (! $this->_is_conn())
	    {
	        return false;
	    }
	    
	    return $this->_stomp->hasFrame();
	}
	
	/**
	 * 启动一个事务
	 * @param string $transaction_id   事务标识
	 * @param array $headers
	 * @return boolean
	 */
	public function begin($transaction_id, $headers = array())
	{
	    if ($transaction_id == '' OR ! $this->_is_conn())
	    {
	        return false;
	    }
	    
	    return $this->_stomp->begin($transaction_id, $headers);
	}
	
	/**
	 * 提交事务
	 * @param string $transaction_id  事务标识
	 * @param array $headers
	 * @return boolean
	 */
	public function commit($transaction_id, $headers = array())
	{
	    if ($transaction_id == '' OR ! $this->_is_conn())
	    {
	        return false;
	    }
	    
	    return $this->_stomp->commit($transaction_id, $headers);
	}
	
	/**
	 * 回滚事务
	 * @param string $transaction_id  事务标识
	 * @param array $headers
	 * @return boolean|unknown
	 */
	public function rollback($transaction_id, $headers = array())
	{
	    if ($transaction_id == '' OR ! $this->_is_conn())
	    {
	        return false;
	    }
	    
	    return $this->_stomp->abort($transaction_id, $headers);
	}
	
	/**
	 * 记录日志
	 * @param unknown $level
	 * @param unknown $message
	 */
	private function setLogs($level, $message){
	    log_message($level, $message, 'stomp_error');
	}


}
// END FTP Class

/* End of file Ftp.php */
/* Location: ./system/libraries/Ftp.php */