<?php
/**
 * Session管理类，用于session的管理 
 *
 */
class PrimarySession
{
	/**
	 * 开始一个会话
	 * @param String session的名称
	 * @return boolean 		返回是否创建成功
	 */
	public function startSession()
	{
		return @session_start();
	}
	
	/**
	 * 获取sessionId
	 * @return	String	会话Id
	 */
	public function getSessionId()
	{
		return session_id();
	}
	
	/**
	 * 设置sessionId		
	 * @param String $sessionId	会话Id
	 */
	public function setSessionId($sessionId)
	{
		session_id($sessionId);
	}
	
	/**
	 * 设置session的变量值
	 * @param String $key		session键名
	 * @param Mix	 $value		session值
	 */
	public function setArg($key ,$value)
	{
		$_SESSION[$key] = $value;
	}
	
	
	/**
	 * 获取session的变量值
	 * @param String $key
	 */
	public function getArg($key )
	{
		if(isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
		}
		return null;
	}
	
	/**
	 * 清除session的所有变量
	 */
	public function clearArgs()
	{
		session_unset();
	}
	
	/**
	 * 结束会话
	 */
	public function close()
	{
		session_unset();
		session_destroy();
	}
}
?>