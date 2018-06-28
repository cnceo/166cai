<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 奖金计算器 模型层
 * @version:V1.2
 * @date:2015-08-14
 */

class Calculate_Model extends MY_Model 
{   
    // 数据源
    private $sourceMap = array(
        '1' => 'Qihui',
        '2' => 'Dcenter'
    );

    // V1.2 支持双色球、大乐透
    private static $LIBRAY_NAMES = array(
        51 => 'ssq',
        23529 => 'dlt'
    );

	public function __construct() 
	{
		parent::__construct();
	}

    /*
     * 奖金计算器 彩种类型
     * @date:2015-08-14
     */
    public function getLibraryName($lotteryId) 
    {
        $libraryName = '';
        if (isset(self::$LIBRAY_NAMES[$lotteryId]))
        {
            $libraryName = self::$LIBRAY_NAMES[$lotteryId];
        }

        return $libraryName;
    }

}