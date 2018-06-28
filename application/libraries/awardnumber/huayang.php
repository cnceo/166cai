<?php

/**
 * 华阳开奖号码抓取服务类
 */
include_once dirname(__FILE__) . '/base.php';
class huayang extends Base
{
    private $pctype_map = array
    (
    	'21406' => '112',
        '21407' => '113',
        '56' => '126',
        '21408' => '124',
    );
    
    public function __construct()
    {
        parent::__construct();
    }
    
    private function cmt_comm($mdid, $body, $datas = array())
    {
        $UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
        // 时间戳
        $timestamp = date('YmdHis', time());
        $digest = $this->getDigest($timestamp, $body);

        $header  = "<?xml version='1.0' encoding='utf-8'?>";
        $header .= "<message version='1.0'>";
        $header .= "<header>";
        $header .= "<messengerid>$UId</messengerid>";
        $header .= "<timestamp>$timestamp</timestamp>";
        $header .= "<transactiontype>$mdid</transactiontype>";
        $header .= "<digest>$digest</digest>";
        $header .= "<agenterid>{$this->CI->config->item('hytob_sellerid')}</agenterid>";
        $header .= "<username>{$this->CI->config->item('hytob_username')}</username>";
        $header .= "</header>";
        $header .= $body;
        $header .= "</message>";

        /*请求前日志记录*/
        $pathTail = "huayang$mdid/" . date('YmdH');
        if(empty($datas['batch'])) $datas['batch'] = $UId;
        $LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
        log_message('LOG', $LogHead . $pathTail, "huayang$mdid/$mdid");
        log_message('LOG', "{$LogHead}[REQ]: " . $header, $pathTail);
        
        $result = $this->CI->tools->request($this->CI->config->item('hytob_pji'), $header, 10);

        /*请求返回日志记录*/
        log_message('LOG', "{$LogHead}[RES]: " . $result, $pathTail);
        $datas['result'] = $result;
        $xmlobj = simplexml_load_string($result);
        $rfun = "result_$mdid";
        return $this->$rfun($xmlobj, $datas);
        
    }

    // 消息包的摘要（时间戳+代理密码+消息体）
    private function getDigest($timestamp, $body)
    {
        return md5($timestamp . $this->CI->config->item('hytob_secret') . $body);
    }
    
    /**
     * 查询开奖号码
     * @param unknown $issue
     * @param unknown $lid
     */
    public function getAwardNumber($issue, $lid)
    {
        $body = "<body>";
        $body .= "<elements>";
        $body .= "<element>";
        $body .= "<lotteryid>".$this->pctype_map[$lid]."</lotteryid>";
        $body .= "<issue>".$issue."</issue>";
        $body .= "</element>";
        $body .= "</elements>";
        $body .= "</body>";
        return $this->cmt_comm('13007', $body, array('issue' => $issue, 'lid' => $lid, 'batch' => "{$lid}-$issue"));
    }
    
    /**
     * 查询号码返回处理
     * @param unknown $xmlobj
     * @param unknown $data
     * @return string|unknown
     */
    private function result_13007($xmlobj, $data)
    {
        $awardNum = '';
        $result = $xmlobj->body->oelement->errorcode;
        if($result == 0)
        {
            $bonuscode = $xmlobj->body->elements->element->bonuscode;
            if($bonuscode != '')
            {
                $awardNum = $bonuscode;
            }
        }
        
        return $awardNum;
    }
}
