<?php
/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：后台消息配置文件
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
$msg_text = array(
    "success" => "恭喜你,操作成功",
    "falied" => "对不起,操作失败",
    "account" => array(
        'name_required' => "用户名不为空",
        "email_failed" => "邮箱不正确",
        "uid_failed" => "用户不存在"
    ),
    "operation" => array(
        'content_required' => "回复不能为空"
    ),
	"channel" => array(
        'content_repeat' => "渠道名称不能重复"
    ),
    'startNumFlag' => "抓取来源至少开启2家",
    'rebateMsg' => array(
    	1 => '网站内没有该用户',
    	2 => '用户名和手机号不一致',
    	3 => '该用户已经参加联盟返利'
    )
    
);
$config['msg_text_cfg'] = $msg_text;
