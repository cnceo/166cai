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