<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
* 参    数：无
* 作    者：wangl
* 功    能：剪切字符串
* 修改日期：2014.11.05
*/
if (!function_exists('cutstr'))
{
    function cutstr($str, $position, $length)
    {
        if (utf8_strlen($str) > $length)
        {
            return utf8_substr($str, $position, $length) . '...';
        }
        else
        {
            return $str;
        }
    }
    
}
/**
* 参    数：无
* 作    者：wangl
* 功    能：计算长度
* 修改日期：2014.11.05
*/
if (!function_exists('utf8_strlen'))
{
    function utf8_strlen($str)
    {
        $count = 0;
        for ($i = 0; $i < strlen($str); $i++)
        {
            $value = ord($str[$i]);
            if ($value > 127)
            {
                //$count++;
                if ($value >= 192 && $value <= 223)
                    $i++;
                elseif ($value >= 224 && $value <= 239)
                    $i += 2;
                elseif ($value >= 240 && $value <= 247)
                    $i += 3;
                else
                    die('Not a UTF-8 compatible string');
            }
            $count++;
        }
        return $count;
    }
    
}
/**
* 参    数：无
* 作    者：wangl
* 功    能：截取字符串
* 修改日期：2014.11.05
*/
if (!function_exists('utf8_substr'))
{
    function utf8_substr($str, $position, $length)
    {
        $start_position = strlen($str);
        $end_position = strlen($str);
        $count = 0;
        $countlen = utf8_strlen($str);
        $start_byte = $countlen;
        if (($position + $length) > $countlen)
            $length = $countlen % $length;
        for ($i = 0; $i < strlen($str); $i++)
        {
            if ($count >= $position && $start_position > $i)
            {
                $start_position = $i;
                $start_byte = $count;
            }
            if (($count - $start_byte) >= $length)
            {
                $end_position = $i;
                break;
            }
            $value = ord($str[$i]);
            if ($value > 127)
            {
                //$count++;
                if ($value >= 192 && $value <= 223)
                    $i++;
                elseif ($value >= 224 && $value <= 239)
                    $i += 2;
                elseif ($value >= 240 && $value <= 247)
                    $i += 3;
                else
                    die('Not a UTF-8 compatible string');
            }
            $count++;
        }
        return (substr($str, $start_position, $end_position - $start_position));
    }
    
}

if (! function_exists ( 'print_str' ))
{
	function print_str($string)
	{
		return empty($string) ? '--' : $string;
	}
}

if (! function_exists ( 'awardsFormat' ))
{
    function awardsFormat($lid = '', $awardNum = '')
    {
        switch ($lid) 
        {
            case '54':
                $formatArr = array(
                    '01' => 'A', 
                    '02' => '2',
                    '03' => '3',
                    '04' => '4',
                    '05' => '5',
                    '06' => '6',
                    '07' => '7',
                    '08' => '8',
                    '09' => '9',
                    '10' => '10',
                    '11' => 'J', 
                    '12' => 'Q', 
                    '13' => 'K', 
                    'S' => '黑桃', 
                    'H' => '红桃', 
                    'C' => '梅花', 
                    'D' => '方块'
                );
                $number = '';
                $awardArr = explode('|', $awardNum);
                $numArr = array_map('trim', explode(',', $awardArr[0]));
                $typeArr = array_map('trim', explode(',', $awardArr[1]));
                for ($i=0; $i < 3; $i++) 
                { 
                    $number .= $formatArr[$typeArr[$i]] . $formatArr[$numArr[$i]] . ' ';
                }
                break;         
            default:
                $number = $awardNum;
                break;
        }
        return $number;
    }
}

if (! function_exists ( 'print_playtype' ))
{
    function print_playtype($lid = '', $playType = '', $playTypeArr = array())
    {
        if($lid == '55')
        {
            if(!empty($playTypeArr))
            {
                foreach ($playTypeArr as $names => $items) 
                {
                    if(in_array($playType, explode(',', $names)))
                    {
                        return $items['name'];
                    }
                }
            }
            return '混合';
        }
        else
        {
            return $playTypeArr[$playType]['name'];
        }
    }
}

// 评论管理 emoji图片
if (! function_exists ( 'emoji4img' ))
{
    function emoji4img($content)
    {
        if(!empty($content))
        {
            $CI = &get_instance();
            $path = '//' . $CI->config->item('base_url') . '/caipiaoimg/static/images/emoji/';

            $content = preg_replace_callback(
                '/(\[.*?\])/',
                function ($matches) use ($path)
                {
                    $match = '';
                    if(!empty($matches[0]))
                    {
                        $match .= '<img class="emojione" src="' . $path .  substr($matches[0], 1, -1) . '.png">';
                    }
                    return $match;
                },
                $content
            );
        }
        return $content;
    }
}

if (! function_exists ( 'calUnitedPoints' ))
{
    function calUnitedPoints($points)
    {
        $pointsMsg = '';
        if($points > 0)
        {
            $huangguan = floor($points / 1000);
            if ($huangguan > 0)
            {
                $pointsMsg .= $huangguan . '皇冠';
            }
            $taiyang = floor(($points - $huangguan * 1000) / 100);
            if ($taiyang > 0)
            {
                $pointsMsg .= $taiyang . '太阳';
            }
            $yueliang = floor(($points - $huangguan * 1000 - $taiyang * 100) / 10);
            if ($yueliang > 0)
            {
                $pointsMsg .= $yueliang . '月亮';
            }
            $xingxing = $points - $huangguan * 1000 - $taiyang * 100 - $yueliang * 10;
            if ($xingxing > 0)
            {
                $pointsMsg .= $xingxing . '星星';
            }
        }
        return $pointsMsg;
    }
}
