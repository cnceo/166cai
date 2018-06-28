<?php

/**
 * 恒钜开奖号码抓取服务类
 */
include_once dirname(__FILE__) . '/base.php';
class shancai extends Base 
{
    
    private $pctype_map = array (
        '57'    => '14001',
        '53'    => '34001',
    );
    
    private function cmt_comm($service, $reqData, $datas = array())
    {
        $UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
        // 请求参数组装
        $reqData['merid'] = $this->CI->config->item('sctob_sellerid');
		$reqData['service'] = $service;
        $reqData['timestamp'] = date('YmdHi', time());
        $reqData['digest'] = $this->getDigest($service, $reqData['timestamp']);

        /*请求前日志记录*/
        $pathTail = "shancai_$service/" . date('YmdH');
		if(empty($datas['batch'])) $datas['batch'] = $UId;
		$LogHead = "{$datas['batch']}-" . md5($service . microtime(true));
		log_message('LOG', $LogHead . $pathTail, "shancai_$service/$service");
		log_message('LOG', "{$LogHead}[REQ]: " . json_encode($reqData), $pathTail);
        $result = $this->CI->tools->request($this->CI->config->item('sctob_pji') . '!' . $service, $reqData);
		$resData = json_decode($result, true);
		
		if($this->CI->tools->recode != 200 || empty($resData)) return ;
		/*请求返回日志记录*/
		log_message('LOG', "{$LogHead}[RES]: " . $result, $pathTail);
		$rfun = "result_$service";
		return $this->$rfun($resData, $datas);
    }
    
    /**
     * [med_queryOpenNotice 开奖号码拉取]
     * @author LiKangJian 2017-11-13
     * @param  [type] $issue [description]
     * @param  [type] $lid   [description]
     * @return [type]        [description]
     */
    public function getAwardNumber($issue, $lid) {
        $issue = $this->formatIssue($issue, $lid);
        $reqData = array(
            'lotteryCode'   =>  $this->pctype_map[$lid],
            'periodCode'    =>  $issue,
        );
        return $this->cmt_comm('queryOpenNotice', $reqData, array('lid'=>$lid,'issue'=>$issue, 'batch' => "{$lid}-$issue"));
    }
    
    private function result_queryOpenNotice($resData, $data) {
        if($resData['resultCode'] == '0000') {
            return $resData['prizeCode'];
        } else {
            return '';
        }
    }
    
    private function getDigest($service, $timestamp)
    {
        return md5($this->CI->config->item('sctob_secret') . $service . $timestamp . $this->CI->config->item('sctob_secret'));
    }
    
    private function formatIssue($issue, $lid, $pre='')
    {
        $issue_format = array('57' => 4, '53' => 0);
        if(empty($pre)) {
            return substr($issue, $issue_format[$lid]);
        } else {
            return "$pre$issue";
        }
    }
}
