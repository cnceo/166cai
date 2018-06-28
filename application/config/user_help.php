<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 新手帮助 彩种配置信息
 * @author liuli
 * @date   2014/12/30
 */

$config['lottery_help'] = array (
	//竞技彩
	'jjc' => array(
		//任选九
        'rj' => array(
        	'sub' => 'guide-pop-jcrj',
            'img' => 'jcrj.png',
            'desc' => '九',
        ),
        //胜负彩
        'sfc' => array(
        	'sub' => 'guide-pop-jcrj',
            'img' => 'jcrj.png',
        	'desc' => '十四',
        )
	),
	//竞彩篮球
    'jclq' => array(
        //混合选
        'hh' => array(
            'sub' => '',
            'img' => 'jclq-index.png',
            'desc' => '请至少选择两场比赛',
        ),
    	//让球胜负
        'rfsf' => array(
        	'sub' => 'guide-pop-jclq-rfsf',
            'img' => 'jclq-rfsf.png',
        	'desc' => '请至少选择两场比赛',
        ),
        //胜负
        'sf' => array(
            'sub' => 'guide-pop-jclq-sf',
            'img' => 'jclq-sf.png',
            'desc' => '请至少选择两场比赛',
        ),
        //大小分
        'dxf' => array(
            'sub' => 'guide-pop-jclq-dxf',
            'img' => 'jclq-dxf.png',
            'desc' => '请至少选择两场比赛',
        ),
        //胜分差
        'sfc' => array(
            'sub' => 'guide-pop-jclq-sfc',
            'img' => 'jclq-sfc.png',
            'desc' => '请至少选择两场比赛',
        )
    ),
    //竞彩足球
    'jczq' => array(
        //混合选
        'hh' => array(
            'sub' => '',
            'img' => 'jczq-index.png',
            'desc' => '竞猜对象不含加时赛和点球大战',
        ),
        //胜平负
        'spf' => array(
            'sub' => 'guide-pop-jczq-spf',
            'img' => 'jczq-spf.png',
            'desc' => '竞猜对象不含加时赛和点球大战',
        ),
        //让球胜平负
        'rqspf' => array(
            'sub' => 'guide-pop-jczq-rqspf',
            'img' => 'jczq-rqspf.png',
            'desc' => '竞猜对象不含加时赛和点球大战',
        ),
        //猜比分
        'cbf' => array(
            'sub' => 'guide-pop-jczq-cbf',
            'img' => 'jczq-cbf.png',
            'desc' => '竞猜对象不含加时赛和点球大战',
        ),
        //总进球
        'jqs' => array(
            'sub' => 'guide-pop-jczq-jqs',
            'img' => 'jczq-jqs.png',
            'desc' => '竞猜对象不含加时赛和点球大战',
        ),
        //总进球
        'bqc' => array(
            'sub' => 'guide-pop-jczq-bqc',
            'img' => 'jczq-bqc.png',
            'desc' => '竞猜对象不含加时赛和点球大战',
        )
    ),
    //数字彩
    'number' => array(
        //福彩3D直选
        'zx' => array(
            'sub' => 'guide-pop-number-fcsd-zx',
            'img' => 'number-fcsd-zx.png',
            'desc' => '',
        ),
        //福彩3D组三
        'z3' => array(
            'sub' => 'guide-pop-number-fcsd-z3',
            'img' => 'number-fcsd-z3.png',
            'desc' => '',
        ),
        //福彩3D组六
        'z6' => array(
            'sub' => 'guide-pop-number-fcsd-z6',
            'img' => 'number-fcsd-z6.png',
            'desc' => '',
        ),
        //排列三直选
        'plszx' => array(
            'sub' => 'guide-pop-number-pls',
            'img' => 'number-fcsd-zx.png',
            'desc' => '',
        ),
        //排列三组三
        'plsz3' => array(
            'sub' => 'guide-pop-number-pls-z3',
            'img' => 'number-fcsd-z3.png',
            'desc' => '',
        ),
        //排列三组六
        'plsz6' => array(
            'sub' => 'guide-pop-number-pls-z6',
            'img' => 'number-fcsd-z6.png',
            'desc' => '',
        ),
        //排列五
        'plw' => array(
            'sub' => 'guide-pop-number-plw',
            'img' => 'number-plw.png',
            'desc' => '',
        ),
        //七乐彩
        'qlc' => array(
            'sub' => 'guide-pop-number-qlc',
            'img' => 'number-qlc.png',
            'desc' => '',
        ),
        //七星彩
        'qxc' => array(
            'sub' => 'guide-pop-number-qxc',
            'img' => 'number-qxc.png',
            'desc' => '',
        ),
        //双色球
        'ssq' => array(
            'sub' => 'guide-pop-number-ssq',
            'img' => 'number-ssq.png',
            'desc' => '',
        ),
        //大乐透
        'dlt' => array(
            'sub' => 'guide-pop-number-dlt',
            'img' => 'number-dlt.png',
            'desc' => '',
        ),
        //大乐透
        'syxw' => array(
            'sub' => 'guide-pop-number-syxw',
            'img' => 'number-syxw.png',
            'desc' => '',
        )
    ),
);
