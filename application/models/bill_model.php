<?php

class Bill_Model extends CI_Model {

    public static $TYPES = array(
        0 => array(
            'name' => '支付',
            'positive' => false,
        ),
        1 => array(
            'name' => '平台补款',
            'positive' => true,
        ),
        2 => array(
            'name' => '平台扣款',
            'positive' => false,
        ),
        3 => array(
            'name' => '平台奖励',
            'positive' => true,
        ),
        4 => array(
            'name' => '团购兑换',
            'positive' => true,
        ),
        5 => array(
            'name' => '平台加奖',
            'positive' => true,
        ),
        6 => array(
            'name' => '平台红包',
            'positive' => true,
        ),
        7 => array(
            'name' => '充值',
            'positive' => true,
        ),
        8 => array(
            'name' => '提款',
            'positive' => false,
        ),
        9 => array(
            'name' => '提成',
            'positive' => true,
        ),
        10 => array(
            'name' => '手工冻结',
            'positive' => false,
        ),
        11 => array(
            'name' => '彩票返奖',
            'positive' => true,
        ),
        12 => array(
            'name' => '充值',
            'positive' => true,
        ),
        13 => array(
            'name' => '系统解冻',
            'positive'  => true,
        ),
        14 => array(
            'name' => '撤单返款',
            'positive' => true,
        ),
        15 => array(
            'name' => '合买返点',
            'positive' => true,
        ),
        16 => array(
            'name' => '合买保底冻结',
            'positive' => true,
        ),
        17 => array(
            'name' => '合买保底退款',
            'positive' => true,
        ),
    );

    public function __construct() {
        parent::__construct();
    }

}
