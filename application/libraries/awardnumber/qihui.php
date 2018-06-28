<?php
/**
 * 齐汇开奖号码抓取服务类
 */
include_once dirname(__FILE__) . '/base.php';
class qihui extends Base
{
    //彩种销售代码
	private $pctype_map = array
	(
		'21406' => 'C511',
	    '54' => 'KLPK3',
	    '21408' => 'HB11X5',
	    '55' => 'CQSSC',
	);
	
	public function __construct()
	{
	    parent::__construct();
		$this->CI->load->library('encrypt_qihui');
	}
	
	/**
	 * 请求公用方法
	 * @param int $mdid    接口编号
	 * @param string $body 请求体
	 * @param array $datas 参数数组
	 * @return unknown
	 */
	private function cmt_comm($mdid, $body, $datas = array())
	{
		$UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
		$body = $this->CI->encrypt_qihui->encrypt($body);
		$header  = "<?xml version='1.0' encoding='utf-8'?>";
		$header .= "<message>";
		$header .= "<head>";
		$header .= "<version>V1</version>";
		$header .= "<command>$mdid</command>";
		$header .= "<venderId>".$this->CI->config->item('qhtob_sellerid')."</venderId>";
		$header .= "<messageId>$UId</messageId>";
		$header .= "<md>" . md5($body) . "</md>";
		$header .= "</head>";
		$header .= "<body>$body</body>";
		$header .= "</message>"; 
		/*请求前日志记录*/
		$pathTail = "qihui$mdid/" . date('YmdH');
		if(empty($datas['batch'])) $datas['batch'] = $UId;
		$LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
		log_message('LOG', $LogHead . $pathTail, "qihui$mdid/$mdid");
		log_message('LOG', "{$LogHead}[REQ]: " . $header, $pathTail);
		
		$result = $this->CI->tools->request($this->CI->config->item('qhtob_pji'), $header, 10);
		/*请求返回日志记录*/
		log_message('LOG', "{$LogHead}[RES]: " . $result, $pathTail);
		
		$xmlobj = simplexml_load_string($result);
		$rfun = "result_$mdid";
		
		return $this->$rfun($xmlobj, $datas);
	}
	
	// 查询开奖号码
	public function getAwardNumber($issue, $lid)
	{
	    $body  = "<?xml version='1.0' encoding='utf-8'?>";
	    $body .= "<body>";
	    $body .= "<lotteryId>{$this->pctype_map[$lid]}</lotteryId>";
	    $body .= "<issue>{$issue}</issue>";
	    $body .= "</body>";
	    return $this->cmt_comm('1003', $body, array('lid' => $lid, 'issue' => $issue, 'batch' => "{$lid}-$issue"));
	}
	
	/**
	 * 开奖结果查询返回
	 * @param unknown $xmlobj
	 * @param unknown $params
	 */
	private function result_1003($xmlobj, $params)
	{
	    $awardNum = '';
		if($xmlobj->head->result == 0 && md5($xmlobj->body) == $xmlobj->head->md)
		{
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlobj = simplexml_load_string($datas);
			$issue = (string)$xmlobj->issue;
			if((string)$xmlobj->drawCode->baseCode != '' && ($issue == $params['issue']))
			{
				$awardNum = (string)$xmlobj->drawCode->baseCode;
				$awardNum = str_replace(' ', ',', $awardNum);
			}
		}
		
		return $awardNum;
	}
}
