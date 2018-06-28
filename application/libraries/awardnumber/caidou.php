<?php

/**
 * 彩豆开奖号码抓取服务类
 */
include_once dirname(__FILE__) . '/base.php';
class caidou extends Base
{
	private $pctype_map = array
	(
		'21406' => '56',
	    '53' => '66',
	    '21407' => '54',
	    '54' => '57',
	    '21408' => '58',
	    '21421' => '55',
	);
	
	public function __construct()
	{
	    parent::__construct();
	}
	
	/**
	 * 请求公用方法
	 * @param unknown $mdid
	 * @param unknown $body
	 * @param unknown $datas
	 * @return unknown
	 */
	private function cmt_comm($mdid, $body, $datas)
	{
		$UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
		$header  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		$header .= "<request>";
		$header .= "<head sid=\"$mdid\" agent=\"" . $this->CI->config->item('cdtob_sellerid') 
				.  "\" messageid=\"$UId\" timestamp=\"" . date('Y-md H:i:s') . "\" memo=\"$mdid\" />";
		$header .= "<body>";
		$header .= $body;
		$header .= "</body>";
		$header .= "</request>";
		$content['xml'] = $header;
		$content['sign'] = md5($header . $this->CI->config->item('cdtob_secret'));
		/*请求前日志记录*/
		$pathTail = "caidou$mdid/" . date('YmdH');
		if(empty($datas['batch'])) $datas['batch'] = $UId;
		$LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
		log_message('LOG', $LogHead . $pathTail, "caidou$mdid/$mdid");
		log_message('LOG', "{$LogHead}[REQ]: " . $header, $pathTail);
		
		$result = $this->CI->tools->request($this->CI->config->item('cdtob_pji'), $content, 10);
		/*请求返回日志记录*/
		log_message('LOG', "{$LogHead}[RES]: " . preg_replace('/[\n\r]+/is', '', $result), $pathTail);
		$xmlobj = simplexml_load_string($result) ;
		$rfun = "result_$mdid";
		
		return $this->$rfun($xmlobj, $datas);
	}
	
	/**
	 * 查询开奖号码
	 * @param unknown $issue
	 * @param unknown $lid
	 */
	public function getAwardNumber($issue, $lid)
	{
		// 快乐扑克期次处理
		$pissue = ($lid == 54) ? '20' . $issue : $issue;
		$body = "<query gid=\"{$this->pctype_map[$lid]}\" pid=\"{$pissue}\"/>";
		return $this->cmt_comm('20002', $body, array('lid' => $lid, 'issue' => $issue, 'batch' => "{$lid}-$issue"));
	}
	
	/**
	 * 查询号码返回处理
	 * @param unknown $xmlobj
	 * @param unknown $data
	 * @return string|unknown
	 */
	private function result_20002($xmlobj, $data)
	{
	    $awardNum = '';
		$result = intval($xmlobj->result['code']);
		if($result == 0)
		{
			$row = $xmlobj->body->rows->row[0];
			$awardNum = $row['awardcode'];
			if($awardNum != '')
			{
				// 快乐扑克号码处理
				if($data['lid'] == 54)
				{
				    $awardNum = $this->klpkAwardFormat($awardNum);
				}
			}
		}

		return $awardNum;
	}
	
	/**
	 * 快乐扑克号码格式化
	 * @param unknown $awardNum
	 * @return string
	 */
	public function klpkAwardFormat($awardNum)
	{
		$klpkType = array(
			'1' => 'S',	// 黑
			'2' => 'H',	// 红
			'3' => 'C',	// 梅
			'4' => 'D',	// 方
		);
		$number = '';
		$p1 = array();
		$p2 = array();
		$awardNumArr = array_map('trim', explode(',', $awardNum));
		if($awardNumArr)
		{
			foreach ($awardNumArr as $nums) 
			{
				array_push($p1, substr($nums, 1, 2));
				$type = substr($nums, 0, 1);
				array_push($p2, $klpkType[$type]);
			}
		}
		$number = implode(',', $p1) . '|' . implode(',', $p2);
		return $number;
	}
}
