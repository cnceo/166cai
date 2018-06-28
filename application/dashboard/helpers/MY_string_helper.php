<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

if (! function_exists ( 'parse_shop_status' ))
{

	function parse_shop_status($status)
	{
		switch ($status)
		{
			case 0 :
				return '待审核';
				break;
			case 10 :
				return '审核未通过';
				break;
			case 20 :
				return '审核通过';
				break;
			case 30 :
				return '上架';
				break;
			case 40 :
				return '下架';
				break;
		}
	}
}

if (! function_exists ( 'print_str' ))
{
	function print_str($string)
	{
		echo empty($string) ? '--' : $string;
	}
}