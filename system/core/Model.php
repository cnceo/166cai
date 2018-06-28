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
 * CodeIgniter Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		log_message('debug', "Model Class Initialized");
	}

	/**
	 * __get
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string
	 * @access private
	 */
	function __get($key)
	{
		$CI =& get_instance();
		if(in_array($key, array('db', 'dc', 'cfgDB', 'slave', 'slaveDc', 'slaveCfg', 'slave1', 'slaveDc1', 'slaveCfg1', 'data',  'master', 'BcdDb')))
		{
			if(empty($CI->$key))
			{
				$CI->$key = $this->getDB($key);
			}
		}
		return $CI->$key;
	}
	/**
	 * 解决数据库链接重复问题
	 */
    private function getDB($DB)
    {
        $dbname = '';
        $dbcfg = array('master' => 'default', 'db' => 'default', 'dc' => 'dc', 'cfgDB' => 'cfg', 'data' => 'data', 'slaveCfg' => 'slaveCfg', 'BcdDb' => 'BcdDb',
            'slaveCfg1' => 'slaveCfg1', 'slave' => array('slave1' => 50, 'slave2' => 50), 'slaveDc' => array('slaveDc1' => 50, 'slaveDc2' => 50));
        if(is_array($dbcfg[$DB])){
            $dbname = $this->getDbname($dbcfg[$DB]);
        }else{
            $dbname = $dbcfg[$DB];
        }
        $this->$DB = $this->load->database($dbname, true);
        return $this->$DB;
    }

    protected function getDbname($rateArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($rateArr);
        //概率数组循环
        foreach ($rateArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($rateArr);
        return $result;
    }
}
// END Model Class

/* End of file Model.php */
/* Location: ./system/core/Model.php */
