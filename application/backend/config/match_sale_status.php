<?php
/**
 * Copyright (c) 2012,上海瑞创网络科技股份有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2015/6/10
 * 修改时间: 13:19
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$statusJCZQ = array(
    'spf'   => 1,
    'rqspf' => 2,
    'bqc'   => 4,
    'jqs'   => 8,
    'bf'    => 16,
);

$statusJCLQ = array(
    'sf'   => 1,
    'rfsf' => 2,
    'sfc'  => 4,
    'dxf'  => 8,
);

$statusBJDC = array(
    'spf'  => 1,
    'bqc'  => 2,
    'jqs'  => 4,
    'sxds' => 8,
    'bf'   => 16,
);

$statusBDSFGG = array(
    'sfgg' => 32,
);

$config['saleStatus'] = array(
    'jczq'   => $statusJCZQ,
    'jclq'   => $statusJCLQ,
    'bjdc'   => $statusBJDC,
    'bdsfgg' => $statusBDSFGG,
);

$statusNameJCZQ = array(
    'spf'   => '胜平负',
    'rqspf' => '让球胜平负',
    'bqc'   => '半全场',
    'jqs'   => '进球数',
    'bf'    => '比分',
);

$statusNameJCLQ = array(
    'sf'   => '胜负',
    'rfsf' => '让分胜负',
    'sfc'  => '胜分差',
    'dxf'  => '大小分',
);

$statusNameBJDC = array(
    'spf'  => '胜平负',
    'bqc'  => '半全场',
    'jqs'  => '进球数',
    'sxds' => '上下单双',
    'bf'   => '比分',
);

$statusNameBDSFGG = array(
    'sfgg' => '胜负过关',
);

$config['saleStatusName'] = array(
    'jczq'   => $statusNameJCZQ,
    'jclq'   => $statusNameJCLQ,
    'bjdc'   => $statusNameBJDC,
    'bdsfgg' => $statusNameBDSFGG,
);