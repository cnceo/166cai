<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//订单状态定义
$config['cfg_orders'] = array(
    'create_init'     => 0, //订单初始化
    'create'          => 10, //创建
    'out_of_date'     => 20, //过期未付款
    'out_of_date_pay' => 21, //过期已付款
    'pay_fail'        => 30, //付款失败，等待系统再付款
    'pay'             => 40, //已付款
    'qualified'       => 200, //未满足条件
    'drawing'         => 240, //出票中(拆票完成)
    'draw'            => 500, //出票成功
	'draw_part'       => 510, //部分出票成功
    'concel'          => 600, //出票失败
	'revoke_by_user'  => 601, //【追号订单表】用户手动撤单
	'revoke_by_system'=> 602, //【追号订单表】系统期次撤单
	'revoke_by_award' => 603, //【追号订单表】中奖后撤单
    'notwin'          => 1000, //未中
    'win'             => 2000, //中奖

    'split_ini'       => 0,    //拆票完成
    'split_ggwin'     => 1010, //过关已确定中奖(jz/jl)

    'paiqi_complete'  => 50, //排期已出结果
    'paiqi_ggsucc'    => 60, //排期表已过关

    'paiqi_jjsucc'    => 60, //排期表已计奖
    'paiqi_awarding'  => 70, //排期表派奖中
    'paiqi_awarded'   => 80, //完成派奖

    'relation_ggsucc' => 550, //已过关成功
    'relation_jjsucc' => 650, //计算奖金成功
);

$config['cfg_lidmap'] = array(
    '11'    => 'sfc',
    '19'    => 'rj',
    '33'    => 'pls',
    '35'    => 'plw',
    '42'    => 'jczq',
    '43'    => 'jclq',
    '51'    => 'ssq',
    '52'    => 'fcsd',
    '10022' => 'qxc',
    '21406' => 'syxw',
    '23528' => 'qlc',
    '23529' => 'dlt',
	'53'    => 'ks',
    '56'    => 'jlks',
	'44'	=> 'gj',
	'45'	=> 'gyj',
	'21407' => 'jxsyxw',
	'21408' => 'hbsyxw',
    '54'    => 'klpk',
    '55'    => 'cqssc',
    '57'    => 'jxks',
    '21421' => 'gdsyxw',
);

//拆票子订单中彩中编号映射
$config['cfg_nlid'] = array(
	'11'    => '01',
	'19'    => '02',
	'33'    => '03',
	'35'    => '04',
	'41'    => '05',
	'42'    => '06',
	'43'    => '07',
	'51'    => '08',
	'52'    => '09',
	'10022' => '10',
	'21406' => '11',
	'21407' => '12',
	'23528' => '13',
	'23529' => '14',
	'53'    => '15',
	'44'	=> '16',
	'45'	=> '17',
	'21408' => '18',
    '54'    => '19',
    '55'    => '20',
    '56'    => '21',
    '57'    => '22',
    '21421' => '23',
);

$config['cfg_errnum'] = array(
    '100001',
    '100004',
    '200001',
    '200006',
    '200009',
    '200014'
);
