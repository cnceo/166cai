<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 导出Excel文件的入口
 * 作    者: 刁寿钧
 * 修改日期: 2015/6/23
 * 修改时间: 16:44
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/third_party/PHPExcel.php';

class Excel extends PHPExcel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getColumnForXls($num)
    {
        $arr = array(0  => 'Z',
                     1  => 'A',
                     2  => 'B',
                     3  => 'C',
                     4  => 'D',
                     5  => 'E',
                     6  => 'F',
                     7  => 'G',
                     8  => 'H',
                     9  => 'I',
                     10 => 'J',
                     11 => 'K',
                     12 => 'L',
                     13 => 'M',
                     14 => 'N',
                     15 => 'O',
                     16 => 'P',
                     17 => 'Q',
                     18 => 'R',
                     19 => 'S',
                     20 => 'T',
                     21 => 'U',
                     22 => 'V',
                     23 => 'W',
                     24 => 'X',
                     25 => 'Y',
                     26 => 'Z'
        );
        if ($num == 0) {
            return '';
        }

        return $this->getColumnForXls((int)(($num - 1) / 26)) . $arr[$num % 26];
    }

}