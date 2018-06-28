<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Lib_Comm
{

	public function __construct()
	{
		
	}
	
	public function format_issue($issue, $start, $len = 0)
	{
		$issue = preg_replace('/[^\d]/is', '', $issue);
		if($len)
		{
			return substr($issue, $start, $len);
		}
		else
		{
			return substr($issue, $start);
		}
	}
	
	public function score_filter($socre)
	{
		$socre = preg_replace('/[^\d:]/is', '', $socre);
		if(preg_match('/^\d+:\d+$/i', $socre))
		{
			return $socre;
		}
		else
		{
			return '';
		}
		
	}
	
	public function getRStatus($datas, $add = array())
	{
		$rstatus = 0;
		if(!empty($datas))
		{
			foreach ($datas as $data)
			{
				if(in_array($add['ctype'], array('dlt')))
				{
					foreach ($data as $row)
					{
						if(intval($row['zs']) > 0)
						{
							$rstatus = 1;
						}
					}
					if($rstatus) break;
				}
				else 
				{
					if(intval($data['zs']) > 0)
					{
						$rstatus = 1;
						break;
					}
				}
			}
		}
		if(empty($rstatus) && !empty($add['ctype']))
		{
			$CI = & get_instance();
        	$CI->load->model('issue_model');
        	$isuues = $CI->issue_model->getNumberInfo($add['issue'], $add['ctype']);
        	if(!empty($isuues)) $rstatus = 1;
		}
		return $rstatus;
	}
	
	public function getStatus($score, $add = array())
    {
    	$status = 1;
        if(!empty($score) && strpos($score, '*') === FALSE)
        {
            $status = 1;
        }
        else
        {
        	if(!(strpos($score, '*') === FALSE) && in_array($add['lid'], array('tczq')))
        	{
        		$scores = explode(',', $score);
        		$CI = & get_instance();
        		$CI->load->model('issue_model');
        		$mnames = $CI->issue_model->getTczqInfo($add['issue'], $add['ctype']);
        		$spans = array('1' => 1, '2' => 2, '3' => 2);
        		foreach ($scores as $in => $score)
        		{
        			$score = trim($score);
        			$mname = intval($in /$spans[$add['ctype']])  + 1;
        			if($score == '*' && !in_array($mname, $mnames))
        			{
        				$status = 0;
        				break;
        			}
        		}
        	}
        	else 
        	{
            	$status = 0;
        	}
        }
        return $status;
    }
    
    public function format_num($val)
    {
    	$val = preg_replace('/[^\d\.]/is', '', $val);
    	if(empty($val))
    	{
    		return '0';
    	}
    	else 
    	{
	    	return preg_replace('/\.\d+$/is', '', $val);
    	}
    }

    //获取体彩足球比分
    public function getTczqScore($data)
    {
    	$scoreData = array();
    	if(!empty($data))
    	{
    		$data = json_decode($data);
    		foreach ($data as $match) 
    		{
    			if(empty($match[1]))
    			{
    				$scoreData[$match[0]][0] = "";
    				$scoreData[$match[0]][1] = "";
    			}
    			else
    			{
    				$score1 = explode(',', $match[2]);
	    			$score2 = explode(',', $match[3]);
	    			$scoreData[$match[0]][0] = "{$score1[0]}:{$score2[0]}";
	    			$scoreData[$match[0]][1] = "{$score1[1]}:{$score2[1]}";
    			}    			
    		}
    	}
    	return $scoreData;
    }
}