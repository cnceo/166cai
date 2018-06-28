<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/errors.html
 */
class CI_Log {

	protected $_log_path;
	protected $_threshold	= 1;
	protected $_date_fmt	= 'Y-m-d H:i:s';
	protected $_enabled	= TRUE;
	protected $_levels	= array('LOG' => '1', 'ERROR' => '2', 'DEBUG' => '3',  'INFO' => '4', 'ALL' => '5');

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$config =& get_config();

		$this->_log_path = ($config['log_path'] != '') ? $config['log_path'] : APPPATH.'logs/';

		if ( ! is_dir($this->_log_path) OR ! is_really_writable($this->_log_path))
		{
			$this->_enabled = FALSE;
		}

		if (is_numeric($config['log_threshold']))
		{
			$this->_threshold = $config['log_threshold'];
		}

		if ($config['log_date_format'] != '')
		{
			$this->_date_fmt = $config['log_date_format'];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	the error level
	 * @param	string	the error message
	 * @param	bool	whether the error is a native PHP error
	 * @return	bool
	 */
	public function write_log($level = 'error', $msg, $php_error = FALSE)
	{
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
		{
			return FALSE;
		}

		$filepath = $this->_log_path.'log-'.date('Y-m-d').'.php';
		if(!empty($php_error) && is_string($php_error))
		{
			preg_match('/^(.*?\/)?([^\/]*)$/i', $php_error, $match);
			$LogPath = $this->_log_path . $match[1];
			if(!is_dir($LogPath))
			{
				$this->mkdirs($LogPath);
				@chgrp($LogPath, 'httpd');
				@chown($LogPath, 'httpd');
			}
			$filepath = $LogPath . 'log-' . date('Y-m-d') . (empty($match[2]) ? '' : "-{$match[2]}") . '.php';
		}
		
		$message  = '';

		if ( ! file_exists($filepath))
		{
			$message .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
		}

		$message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";

		error_log ( $message ,  3 ,  $filepath );

		@chmod($filepath, FILE_WRITE_MODE);
		return TRUE;
	}
	
	private function mkdirs($dir, $mode = 0777)  
    {  
	    if (is_dir($dir) || @mkdir($dir, $mode)) 
	    	return TRUE;  
	    if (!mkdirs(dirname($dir), $mode)) 
	    	return FALSE;  
	    return @mkdir($dir, $mode);  
    } 

}
// END Log Class

/* End of file Log.php */
/* Location: ./system/libraries/Log.php */