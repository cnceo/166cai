<?php
class HandleCqssc
{
	
	public function dxds($num)
	{
		$str = '';
		$num = (int)$num;
		if ($num < 5) 
		{
			$str .= '小';
		}
		else 
		{
			$str .= '大';
		}
		if ($num%2 == 1)
		{
			$str .= '单';
		}
		else 
		{
			$str .= '双';
		}
		return $str;
	}
	
	public function xingtai($array)
	{
		$count = count(array_unique($array));
		switch ($count)
		{
			case 1:
				return '豹子';
				break;
			case 2:
				return '组三';
				break;
			case 3:
			default:
				return '组六';
				break;
		}
	}
}
